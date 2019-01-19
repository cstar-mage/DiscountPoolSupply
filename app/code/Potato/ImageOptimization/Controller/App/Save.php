<?php

namespace Potato\ImageOptimization\Controller\App;

use Magento\Framework\App\Action\Context;
use Potato\ImageOptimization\Api\ImageRepositoryInterface;
use Potato\ImageOptimization\Model\App\ImageOptimization;
use Magento\Framework\Controller\ResultFactory;
use Potato\ImageOptimization\Model\Source\Image\Status as StatusSource;
use Potato\ImageOptimization\Logger\Logger;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Potato\ImageOptimization\Model\Source\Optimization\Error as ErrorSource;

/**
 * Class Save
 */
class Save extends \Magento\Framework\App\Action\Action
{
    /** @var ImageRepositoryInterface  */
    protected $imageRepository;

    /** @var ImageOptimization  */
    protected $appImageOptimization;

    /** @var Logger  */
    protected $logger;

    /**
     * Save constructor.
     * @param Context $context
     * @param ImageRepositoryInterface $imageRepository
     * @param ImageOptimization $appImageOptimization
     * @param Logger $logger
     */
    public function __construct(
        Context $context,
        ImageRepositoryInterface $imageRepository,
        ImageOptimization $appImageOptimization,
        Logger $logger
    ) {
        parent::__construct($context);
        $this->imageRepository = $imageRepository;
        $this->appImageOptimization = $appImageOptimization;
        $this->logger = $logger;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        $resultForward->forward('noroute');
        try {
            $optimizationResult = $this->getRequest()->getParam('optimization_result');
            $images = $this->appImageOptimization->getOptimizedImages($optimizationResult);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
            return $resultForward;
        }
        
        /** @var  $image \Potato\ImageOptimization\Model\App\Image\Result;*/
        foreach ($images as $image) {
            $imagePath = $this->createImagePathFromUrl($image->getOriginalUrl());
            if ($image->getAlternativeUrl()) {
                $imagePath = $this->createImagePathFromUrl($image->getAlternativeUrl());
            }
            
            try {
                $imageEntity = $this->imageRepository->getByPath($imagePath);
            } catch (NoSuchEntityException $e) {
                $this->logger->error($e->getMessage());
                continue;
            }    
            
            $optimizedImage = @file_get_contents($image->getOptimizedUrl());
            if (false === $optimizedImage) {
                $imageEntity
                    ->setStatus(StatusSource::STATUS_ERROR)
                    ->setErrorType(ErrorSource::IS_NOT_READABLE)
                    ->setResult(
                        __("The optimized image can't be retrieved from the service. Path to file: %1
                            Possible solution: Submit a support ticket 
                            <a href='https://potatocommerce.com/contacts/'>here</a>",
                        $image->getOptimizedUrl())
                    );
                $this->imageRepository->save($imageEntity);
                continue;
            }

            $result = file_put_contents($imagePath, $optimizedImage);
            if (false === $result) {
                $imageEntity
                    ->setStatus(StatusSource::STATUS_ERROR)
                    ->setErrorType(ErrorSource::CANT_UPDATE)
                    ->setResult(__("Can't update the file. Please check the file permissions."));
                $this->imageRepository->save($imageEntity);
                continue;
            }
            $imageEntity->setStatus(StatusSource::STATUS_OPTIMIZED);
            if (!$image->isOptimized()) {
                $imageEntity
                    ->setErrorType(ErrorSource::APPLICATION)
                    ->setStatus(StatusSource::STATUS_ERROR);
            }
            $imageEntity
                ->setPath($imagePath)
                ->setResult($image->getResult())
                ->setTime(filemtime($imagePath))
            ;
            $this->imageRepository->save($imageEntity);
        }
        
        return $this;
    }

    /**
     * @param string $imageUrl
     * @return string
     */
    private function createImagePathFromUrl($imageUrl)
    {
        $secure = false;
        if (preg_match('/^https:\/\//', $imageUrl)) {
            $secure = true;
        }
        return str_replace(
            trim($this->_url->getBaseUrl(['_type' => UrlInterface::URL_TYPE_WEB, '_secure' => $secure]), '/'),
            BP,
            $imageUrl
        );
    }
}
