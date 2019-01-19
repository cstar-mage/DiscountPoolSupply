<?php

namespace Potato\ImageOptimization\Model\Image\Gif;

use Potato\ImageOptimization\Api\Data\ImageInterface;
use Potato\ImageOptimization\Model\Source\Image\Status as StatusSource;
use Potato\ImageOptimization\Model\Source\Optimization\Error as ErrorSource;
use Potato\ImageOptimization\Model\Image\AbstractUtility;

/**
 * Class Gifsicle
 */
class Gifsicle extends AbstractUtility
{
    const LIB_PATH = 'gifsicle';
    const DEFAULT_OPTIONS = '-b -O3';

    /**
     * @param ImageInterface $image
     * @return ImageInterface
     * @throws \Exception
     */
    public function optimize(ImageInterface &$image)
    {
        if ($this->config->canUseService()) {
            return $this->sendToService($image);
        }
        $beforeFilesize = filesize($image->getPath());
        exec(
            self::LIB_PATH . ' ' . self::DEFAULT_OPTIONS . ' "' . $image->getPath() . '" 2>&1',
            $result,
            $error
        );
        $stringResult = implode(' ', $result);

        if ($error != 0 && strpos($stringResult, 'gifsicle:   trailing garbage after GIF ignored') === false) {
            $image->setErrorType(ErrorSource::APPLICATION);
            throw new \Exception(__('Application for GIF files optimization returns the error. Error code: %1 %2',
                $error, $stringResult));
        }
        $afterFilesize = filesize($image->getPath());
        $image
            ->setStatus(StatusSource::STATUS_OPTIMIZED)
            ->setErrorType('')
            ->setResult(__("%1 bytes -> %2 bytes optimized", $beforeFilesize, $afterFilesize));
        return $image;
    }
}
