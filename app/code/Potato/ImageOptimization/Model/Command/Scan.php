<?php
namespace Potato\ImageOptimization\Model\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Store\Model\StoreManagerInterface;
use Potato\ImageOptimization\Logger\Logger;
use Magento\Framework\App\CacheInterface;
use Potato\ImageOptimization\Model\Config;
use Potato\ImageOptimization\Model\Manager\Scanner as ScannerManager;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Symfony\Component\Console\Helper\ProgressBar;

class Scan extends Command
{
    const INPUT_KEY_LIMIT = 'limit';
    const INPUT_KEY_START_DIR = 'start_dir';
    
    /** @var Logger  */
    protected $logger;

    /** @var CacheInterface  */
    protected $cache;

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /** @var Config  */
    protected $config;

    /** @var ScannerManager  */
    protected $scanner;

    /** @var Filesystem  */
    protected $filesystem;

    /** @var ProgressBar */
    protected $progress;

    /**
     * Scan constructor.
     * @param StoreManagerInterface $storeManager
     * @param CacheInterface $cache
     * @param Logger $logger
     * @param Config $config
     * @param ScannerManager $scanner
     * @param Filesystem $filesystem
     * @param null $name
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        CacheInterface $cache,
        Logger $logger,
        Config $config,
        ScannerManager $scanner,
        Filesystem $filesystem,
        $name = null
    ) {
        parent::__construct($name);
        $this->storeManager = $storeManager;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->scanner = $scanner;
        $this->filesystem = $filesystem;
        $this->config = $config;
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('po_image_optimization:scan')
            ->setDefinition([
                new InputOption(
                    self::INPUT_KEY_LIMIT,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Scan until found image count < limit'
                ),
                new InputOption(
                    self::INPUT_KEY_START_DIR,
                    null,
                    InputOption::VALUE_OPTIONAL,
                    'Scan from this dir'
                )
            ])
            ->setDescription('Potato Image Optimizer: manually scan via console');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return $this
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (true === $this->cache->load(Config::SCAN_RUNNING_CACHE_KEY)) {
            $output->writeln('Scanner is already running. Clean cache if you want run scanner again');
            return $this;
        }
        $this->cache->save(true, Config::SCAN_RUNNING_CACHE_KEY);
        $limit = null;
        if ($input->getOption(self::INPUT_KEY_LIMIT)) {
            $limit = $input->getOption(self::INPUT_KEY_LIMIT);
        }
        $startDir = null;
        if ($input->getOption(self::INPUT_KEY_START_DIR)) {
            $basePath = $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath();
            $startDir = $basePath . trim($input->getOption(self::INPUT_KEY_START_DIR), '/');
        }
        $this->progress = new ProgressBar($output, $limit);
        $this->progress->setFormat('<comment>%message%</comment> %current%');
        $this->progress->setMessage(__('Search images in dirs'));
        $this->scanner->setCallback([$this, 'updateProgress']);
        if ($startDir) {
            $this->scanner->prepareImagesFromDir($startDir, $limit);
        } else {
            $this->scanner->saveImageGalleryFiles($limit);
        }
        $output->writeln("");
        $progress = new ProgressBar($output, $limit);
        $progress->setFormat('<comment>%message%</comment> %current%');
        $progress->setMessage(__('Update images from database'));
        $this->scanner->updateImagesFromDatabase();
        while($this->cache->load(ScannerManager::SCAN_DATABASE_STATUS_CACHE_KEY)){
            $this->scanner->updateImagesFromDatabase();
            $progress->advance();
        }
        $output->writeln("");
        $this->cache->remove(Config::SCAN_RUNNING_CACHE_KEY);
        return $this;
    }

    public function updateProgress()
    {
        $this->progress->advance();
    }
}