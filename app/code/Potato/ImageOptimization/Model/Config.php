<?php
namespace Potato\ImageOptimization\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Potato\ImageOptimization\Model\Source\System\OptimizationMethod;

/**
 * Class Config
 */
class Config
{
    const GENERAL_ENABLED           = 'potato_image_optimization/general/is_enabled';
    const GENERAL_IMAGE_BACKUP      = 'potato_image_optimization/general/image_backup';
    const OPTIMIZATION_METHOD       = 'potato_image_optimization/general/optimization_method';

    const JPEG_COMPRESSION_LEVEL    = 'potato_image_optimization/jpg/compression_level';

    const DEFAULT_FOLDER_PERMISSION = 0775;
    const DEFAULT_FILE_PERMISSION = 0664;

    const SCAN_RUNNING_CACHE_KEY = 'po_imageoptimization_SCAN_RUNNING';
    const OPTIMIZATION_RUNNING_CACHE_KEY = 'po_imageoptimization_OPTIMIZTION_RUNNING';

    /** @var ScopeConfigInterface  */
    protected $scopeConfig;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /**
     * Config constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }
    
    /**
     * @param int|null $storeId
     * @return bool
     */
    public function canUseService($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        $result = $this->scopeConfig->getValue(
            self::OPTIMIZATION_METHOD,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        return $result === OptimizationMethod::USE_SERVICE;
    }

    /**
     * @return bool
     */
    public function isAllowImageBackup()
    {
        return (bool)$this->scopeConfig->getValue(
            self::GENERAL_IMAGE_BACKUP
        );
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            self::GENERAL_ENABLED
        );
    }

    /**
     * @return string
     */
    public function getCompressionLevel()
    {
        return $this->scopeConfig->getValue(
            self::JPEG_COMPRESSION_LEVEL
        );
    }

    /**
     * @return int
     */
    public function getFolderPermission()
    {
        return self::DEFAULT_FOLDER_PERMISSION;
    }

    /**
     * @return int
     */
    public function getFilePermission()
    {
        return self::DEFAULT_FILE_PERMISSION;
    }
}
