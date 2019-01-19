<?php

namespace Potato\ImageOptimization\Model\Image\Png;

use Potato\ImageOptimization\Api\Data\ImageInterface;
use Potato\ImageOptimization\Model\Source\Image\Status as StatusSource;
use Potato\ImageOptimization\Model\Image\AbstractUtility;
use Potato\ImageOptimization\Model\Source\Optimization\Error as ErrorSource;


/**
 * Class Optipng
 */
class Optipng extends AbstractUtility
{
    const LIB_PATH = 'optipng';
    const DEFAULT_OPTIONS = '-o7 -clobber -strip all';

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

        if (empty($result) || $error != 0) {
            $image->setErrorType(ErrorSource::APPLICATION);
            throw new \Exception(__('Application for PNG files optimization returns the error. Error code: %1 %2',
                $error, $stringResult));
        }
        clearstatcache($image->getPath());
        $afterFilesize = filesize($image->getPath());
        $image
            ->setStatus(StatusSource::STATUS_OPTIMIZED)
            ->setErrorType('')
            ->setResult(__("%1 bytes -> %2 bytes optimized", $beforeFilesize, $afterFilesize));
        return $image;
    }
}
