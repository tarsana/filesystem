<?php namespace Tarsana\IO\Filesystem\Adapters;

use Tarsana\IO\Interfaces\Filesystem\Adapter;


class Local implements Adapter {

    /**
     * The singleton instance.
     *
     * @var self
     */
    protected static $instance = null;

    /**
     * Gets the singleton instance.
     *
     * @return self
     */
    public static function instance()
    {
        if (null === self::$instance) {
            self::$instance = new Local;
        }
        return self::$instance;
    }

    /**
     * Priate Constructor.
     */
    private function __construct() {}

    /**
     * Tells if the given path is absolute.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isAbsolute($path)
    {
        $firstChar = substr($path, 0, 1);
        if ('/' === $firstChar || '\\' === $firstChar) {
            return true;
        }
        $isLetter = (
            ($firstChar >= 'A' && $firstChar <= 'Z') ||
            ($firstChar >= 'a' && $firstChar <= 'z')
        );
        $path = substr($path, 1, 2);
        return $isLetter && (':/' === $path || ':\\' === $path);
    }

    /**
     * Equivalent to PHP `realpath` function.
     *
     * @param  string $path
     * @return array
     */
    public function realpath($path)
    {
        return realpath($path);
    }

    /**
     * Equivalent to PHP `glob` function.
     *
     * @param  string $pattern
     * @return array
     */
    public function glob($pattern)
    {
        return glob($pattern);
    }

    /**
     * Equivalent to PHP `is_file` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isFile($path)
    {
        return is_file($path);
    }

    /**
     * Equivalent to PHP `is_dir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isDir($path)
    {
        return is_dir($path);
    }

    /**
     * Equivalent to PHP `file_exists` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function fileExists($path)
    {
        return file_exists($path);
    }

    /**
     * Equivalent to PHP `md5_file` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function md5File($path)
    {
        return md5_file($path);
    }

    /**
     * Equivalent to PHP `file_get_contents` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function fileGetContents($path)
    {
        return file_get_contents($path);
    }

    /**
     * Equivalent to PHP `file_put_contents` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function filePutContents($path, $content, $flags = 0)
    {
        return file_put_contents($path, $content, $flags);
    }

    /**
     * Equivalent to PHP `is_readable` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isReadable($path)
    {
        return is_readable($path);
    }

    /**
     * Equivalent to PHP `is_writable` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isWritable($path)
    {
        return is_writable($path);
    }

    /**
     * Equivalent to PHP `is_executable` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isExecutable($path)
    {
        return is_executable($path);
    }

    /**
     * Equivalent to PHP `unlink` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function unlink($path)
    {
        return unlink($path);
    }

    /**
     * Equivalent to PHP `rmdir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function rmdir($path)
    {
        return rmdir($path);
    }

    /**
     * Equivalent to PHP `rename` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function rename($oldPath, $newPath)
    {
        return rename($oldPath, $newPath);
    }

    /**
     * Equivalent to PHP `fileperms` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function fileperms($path)
    {
        return fileperms($path);
    }

    /**
     * Equivalent to PHP `chmod` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function chmod($path, $value)
    {
        return chmod($path, $value);
    }

    /**
     * Equivalent to PHP `clearstatcache` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function clearstatcache($clearRealCache, $path)
    {
        return clearstatcache($clearRealCache, $path);
    }

    /**
     * Equivalent to PHP `mkdir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function mkdir($path, $mode = 0777, $recursive = false)
    {
        return mkdir($path, $mode, $recursive);
    }

    /**
     * Creates a new empty file, overrite.
     *
     * @param  string  $path
     * @return boolean
     */
    public function createFile($path)
    {
        $created = fopen($path, "w");
        if (false === $created)
            return false;

        fclose($created);
        return true;
    }

    /**
     * Gets the extension of `$path`.
     *
     * @param  string  $path
     * @return boolean
     */
    public function extension($path)
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    /**
     * Equivalent to PHP `basename` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function basename($path)
    {
        return basename($path);
    }

}
