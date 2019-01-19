<?php
namespace Potato\ImageOptimization\Lib\FileFinder;


class FileFinderWorker
{
    protected $_dir = array();
    protected $_callback = null;
    protected $_iterationLimit = null;
    protected $_startPath = null;

    protected $_iterationCount = 0;
    protected $_callbackCount = 0;
    protected $_lastPath;


    /**
     * @param string $dir
     * @param array $callback
     * @param int $iterationLimit
     * @param null|string $startPath
     */
    public function __construct($dir, $callback, $iterationLimit, $startPath = null)
    {
        $this->_dir = $dir;
        $this->_callback = $callback;
        $this->_iterationLimit = $iterationLimit;
        $this->_startPath = $startPath;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function find()
    {
        if (!is_dir($this->_dir)) {
            throw new \Exception('This directory is not exists or not directory: ' . $this->_dir);
        }
        if (null !== $this->_startPath) {
            $this->_goUp($this->_startPath);
            return $this;
        }
        $this->_readDir($this->_dir);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastPath()
    {
        return $this->_lastPath;
    }

    /**
     * @param string $dirPath
     * @param string $after = null
     *
     * @return $this
     * @throws \Exception
     */
    protected function _readDir($dirPath, $after = null)
    {
        if (!$this->_checkIteration($dirPath)) {
            return $this;
        }
        $list = scandir($dirPath);
        if (FALSE === $list) {
            throw new \Exception('scandir return FALSE for this directory: ' . $dirPath);
        }
        $list = array_diff($list, array('..', '.'));
        foreach ($list as $filename) {
            if (null !== $after && strcmp($filename, $after) <= 0) {//if $filename less or equal $after then
                continue;
            }
            $path = $dirPath . DIRECTORY_SEPARATOR . $filename;
            if (is_dir($path)) {
                unset($list, $dirPath, $filename, $after);
                return $this->_readDir($path);
            }
            $result = $this->_callForFile($path);
            if (!$result) {
                return $this;
            }
        }
        unset($list, $path, $filename, $result, $after);
        $this->_goUp($dirPath);
        return $this;
    }

    /**
     * @param string $dirPath
     *
     * @return $this
     */
    protected function _goUp($dirPath)
    {
        if (!$this->_checkIteration($dirPath)) {
            return $this;
        }
        if ($dirPath === $this->_dir) {
            return $this;
        }
        $list = explode(DIRECTORY_SEPARATOR, $dirPath);
        $filename = array_pop($list);
        $path = join(DIRECTORY_SEPARATOR, $list);
        unset($list, $dirPath);
        $this->_readDir($path, $filename);
        return $this;
    }

    /**
     * @param string $filePath
     *
     * @return $this
     */
    protected function _callForFile($filePath)
    {
        call_user_func($this->_callback, $filePath);
        $this->_callbackCount++;
        return $this;
    }

    /**
     * @param string
     *
     * @return bool
     */
    protected function _checkIteration($path)
    {
        if ($this->_iterationLimit <= $this->_iterationCount) {
            $this->_lastPath = $path;
            return false;
        }
        $this->_iterationCount++;
        return true;
    }
}