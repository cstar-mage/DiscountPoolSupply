<?php
namespace Potato\ImageOptimization\Model\Manager;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Potato\ImageOptimization\Lib\FileFinder\FileFinder;
use Potato\ImageOptimization\Api\ImageRepositoryInterface;
use Potato\ImageOptimization\Model\Source\Image\Status as StatusSource;
use Potato\ImageOptimization\Logger\Logger;

/**
 * Class Scanner
 */
class Scanner
{
    const START_FILE_CACHE_ID = 'po_image_optimization_START_FILE_CACHE_ID';
    const SCAN_DATABASE_STEP = 500;
    const SCAN_DATABASE_STATUS_CACHE_KEY = 'po_imageoptimization_SCAN_DATABASE_STATUS';

    /** @var CacheInterface  */
    protected $cache;

    /** @var Filesystem  */
    protected $filesystem;

    /** @var ImageRepositoryInterface  */
    protected $imageRepository;

    /** @var Logger  */
    protected $logger;
    
    protected $cachePostfix = null;
    
    protected $callbackCount = 0;

    protected $limit = null;
    
    protected $timeLimit = null;
    
    protected $timeStart = null;
    
    protected $originalMaxNestingLevel = null;
    
    protected $callback = null;

    /**
     * Scanner constructor.
     * @param ImageRepositoryInterface $imageRepository
     * @param CacheInterface $cache
     * @param Logger $logger
     * @param Filesystem $filesystem
     */
    public function __construct(
        ImageRepositoryInterface $imageRepository,
        CacheInterface $cache,
        Logger $logger,
        Filesystem $filesystem
    ) {
        $this->cache = $cache;
        $this->filesystem = $filesystem;
        $this->imageRepository = $imageRepository;
        $this->logger = $logger;
    }

    /**
     * @return $this
     */
    public function saveImageGalleryFiles($limit = null)
    {
        $staticPath = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW)->getAbsolutePath();
        $mediaPath = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath();
        $this->prepareImagesFromDir(rtrim($mediaPath, '/'), $limit);
        if (null !== $limit) {
            $limit -= $this->callbackCount;
        }
        $this->prepareImagesFromDir(rtrim($staticPath, '/'), $limit);
        return $this;
    }

    /**
     * @param string $dirPath
     * @param int|null $limit
     * @return $this
     * @throws \Exception
     */
    public function prepareImagesFromDir($dirPath, $limit = null)
    {
        $startDir = null;
        $this->cachePostfix = md5($dirPath);
        if ($this->cache->load(self::START_FILE_CACHE_ID . $this->cachePostfix)) {
            $startDir = $this->cache->load(self::START_FILE_CACHE_ID . $this->cachePostfix);
        }
        $this->limit = $limit;
        $fileFinder = new FileFinder([
            'dir' => $dirPath,
            'callback' => array($this, 'saveFilePath'),
            'start_path' => $startDir
        ]);
        $fileFinder->find();
        return $this;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    public function saveFilePath($filePath)
    {
        if (null !== $this->callback) {
            call_user_func($this->callback);
        }
        if (
            null !== $this->timeLimit && null !== $this->timeStart 
            && $this->timeLimit <= time() - $this->timeStart
        ) {
            return false;
        }
        if (null !== $this->limit && $this->callbackCount >= $this->limit) {
            return false;
        }
        $this->cache->save($filePath, self::START_FILE_CACHE_ID . $this->cachePostfix);
        $result = null;
        if ($this->imageRepository->isPathExist($filePath) || !$this->imageRepository->getImageType($filePath)) {
            return true;
        }
        $image = $this->imageRepository->create();
        $image
            ->setPath($filePath)
            ->setStatus(StatusSource::STATUS_PENDING)
        ;
        try {
            $this->imageRepository->save($image);
            $this->callbackCount++;
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage());
        }
        return true;
    }

    /**
     * @return $this
     */
    public function updateImagesFromDatabase()
    {
        $curPage = $this->cache->load(self::SCAN_DATABASE_STATUS_CACHE_KEY);
        if (!$curPage) {
            $curPage = 0;
        }
        $curPage++;
        $imageList = $this->imageRepository->getListPerPagination(self::SCAN_DATABASE_STEP, $curPage);
        if ($curPage > $imageList->getLastPageNumber()) {
            $this->cache->remove(self::SCAN_DATABASE_STATUS_CACHE_KEY);
            return $this;
        }
        foreach ($imageList->getItems() as $item) {
            if (!file_exists($item->getPath())) {
                $this->imageRepository->delete($item);
                continue;
            }
            if ((filemtime($item->getPath()) <= $item->getTime())
                || $item->getStatus() !== StatusSource::STATUS_OPTIMIZED
            ) {
                continue;
            }
            try {
                $item->setStatus(StatusSource::STATUS_OUTDATED);
                $this->imageRepository->save($item);
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage());
            }
        }
        $this->cache->save($curPage, self::SCAN_DATABASE_STATUS_CACHE_KEY);
        return $this;
    }

    /**
     * @param int $timeLimit
     */
    public function setTimeLimit($timeLimit)
    {
        $this->timeLimit = $timeLimit;
    }

    /**
     * @param int $timeStart
     */
    public function setTimeStart($timeStart)
    {
        $this->timeStart = $timeStart;
    }

    public function setCallback($param)
    {
        $this->callback = $param;
        return $this;
    }
}
