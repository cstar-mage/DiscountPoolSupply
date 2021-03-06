<?php

namespace Potato\ImageOptimization\Model\Image;

use Potato\ImageOptimization\Api\ImageRepositoryInterface;
use Potato\ImageOptimization\Model\Config;
use Potato\ImageOptimization\Model\App;
use Potato\ImageOptimization\Api\UtilityInterface;
use Potato\ImageOptimization\Api\Data\ImageInterface;
use Potato\ImageOptimization\Model\Source\Image\Status as StatusSource;

/**
 * Class AbstractImage
 */
abstract class AbstractUtility implements UtilityInterface
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var App
     */
    protected $app;

    /**
     * @var ImageRepositoryInterface
     */
    protected $imageRepository;

    /**
     * AbstractImage constructor.
     * @param ImageRepositoryInterface $imageRepository
     * @param Config $config
     * @param App $app
     */
    public function __construct(
        ImageRepositoryInterface $imageRepository,
        Config $config,
        App $app
    ) {
        $this->imageRepository = $imageRepository;
        $this->config = $config;
        $this->app = $app;
    }
    
    /**
     * @param ImageInterface $image
     * @return ImageInterface
     */
    protected function sendToService(ImageInterface $image)
    {
        $image
            ->setStatus(StatusSource::STATUS_PENDING_SERVICE)
            ->setResult(__('The image has been prepared to sent to the service.'));
        return $image;
    }
}
