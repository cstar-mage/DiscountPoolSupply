<?php
namespace Potato\ImageOptimization\Model;

use Magento\Framework\UrlInterface;
use Potato\ImageOptimization\Model\App\ImageOptimization as AppImageOptimization;
use Potato\ImageOptimization\Logger\Logger;
use Potato\ImageOptimization\Model\ResourceModel\Image\CollectionFactory as ImageCollectionFactory;
use Potato\ImageOptimization\Model\Source\Image\Status as StatusSource;
use Magento\Framework\App\State;

/**
 * Class App
 */
class App
{
    const SERVICE_IMAGES_DATA_NAME = 'potato_service_images';
    const SERVICE_IMAGES_TRANSFER_LIMIT = 500;

    /** @var ImageCollectionFactory  */
    protected $imageCollectionFactory;

    /** @var UrlInterface  */
    protected $urlBuilder;

    /** @var AppImageOptimization  */
    protected $appImageOptimization;

    /** @var Logger  */
    protected $logger;

    /** @var  State */
    protected $appEmulation;

    /**
     * App constructor.
     * @param ImageCollectionFactory $imageCollectionFactory
     * @param UrlInterface $urlBuilder
     * @param AppImageOptimization $appImageOptimization
     * @param Logger $logger
     * @param State $emulation
     */
    public function __construct(
        ImageCollectionFactory $imageCollectionFactory,
        UrlInterface $urlBuilder,
        AppImageOptimization $appImageOptimization,
        Logger $logger,
        State $emulation
    ) {
        $this->imageCollectionFactory = $imageCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->appImageOptimization = $appImageOptimization;
        $this->logger = $logger;
        $this->appEmulation = $emulation;
    }

    /**
     * @return int
     */
    public function sendServiceImages()
    {
        /** @var \Potato\ImageOptimization\Model\ResourceModel\Image\Collection $imageCollection */
        $imageCollection = $this->imageCollectionFactory->create();
        $imageCollection->addFilterByStatus(StatusSource::STATUS_PENDING_SERVICE);
        $imageCollection->setPageSize(self::SERVICE_IMAGES_TRANSFER_LIMIT);
        $images = $imageCollection->toOptionHash();
        $imagesForService = [];
        foreach ($images as $imagePath) {
            $imagePath = $this->createImageUrlFromPath($imagePath);
            $imagesForService[] = ['url' => $imagePath];
        }
        $imagesForServiceCount = count($imagesForService);
        if (!$imagesForServiceCount) {
            return $imagesForServiceCount;
        }
        try {
            $url = $this->appEmulation->emulateAreaCode(
                \Magento\Framework\App\Area::AREA_FRONTEND,
                [$this->urlBuilder, 'getUrl'],
                ['po_image/app/save']
            );
            $this->appImageOptimization->process($url, $imagesForService);
            foreach ($imageCollection->getItems() as $item) {
                $item
                    ->setStatus(StatusSource::STATUS_SERVICE)
                    ->setResult(__('The image has been transferred to the service. Waiting for complete optimization'))
                    ->save();
            }
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }
        return $imagesForServiceCount;
    }

    /**
     * @param string $imagePath
     * @return string
     */
    private function createImageUrlFromPath($imagePath)
    {
        $url = $this->appEmulation->emulateAreaCode(
            \Magento\Framework\App\Area::AREA_FRONTEND,
            [$this->urlBuilder, 'getBaseUrl'],
            [UrlInterface::URL_TYPE_WEB]
        );        
        return str_replace(
            BP,
            trim($url, '/'),
            $imagePath
        );
    }
}
