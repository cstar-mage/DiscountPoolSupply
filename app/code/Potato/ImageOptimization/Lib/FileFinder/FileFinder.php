<?php
namespace Potato\ImageOptimization\Lib\FileFinder;

/**
 * Class FileFinder
 */
class FileFinder
{
    const ITERATION_LIMIT = 1000;

    protected $_dir = null;
    protected $_callback = null;
    protected $_startPath = null;

    /**
     * @param array $config
     */
    public function __construct($config)
    {
        $this->_dir = $config['dir'];
        $this->_callback = $config['callback'];
        if (array_key_exists('start_path', $config)) {
            $this->_startPath = $config['start_path'];
        }
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function find()
    {
        $originalIni = ini_get('xdebug.max_nesting_level');
        ini_set('xdebug.max_nesting_level', self::ITERATION_LIMIT * 10);

        $startPath = $this->_startPath;
        while(true) {
            $result = $this->_runWorker(self::ITERATION_LIMIT, $startPath);
            if (null === $result) {
                break;
            }
            $startPath = $result;
        }

        ini_set('xdebug.max_nesting_level',$originalIni);
        return $this;
    }

    /**
     * @param int $iterationLimit
     * @param string $fromPath
     *
     * @return string|null
     * @throws \Exception
     */
    protected function _runWorker($iterationLimit, $fromPath)
    {
        $worker = new FileFinderWorker($this->_dir, $this->_callback, $iterationLimit, $fromPath);
        $worker->find();
        return $worker->getLastPath();
    }
}
