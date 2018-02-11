<?php namespace Tarsana\Filesystem\Interfaces;

/**
 * Filesystem adapter: defines the low level functions for a specific filesystem
 */
interface Adapter {

    /**
     * Tells if the given path is absolute.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isAbsolute($path);

    /**
     * Equivalent to PHP `realpath` function.
     *
     * @param  string $path
     * @return array
     */
    public function realpath($path);

    /**
     * Equivalent to PHP `glob` function.
     *
     * @param  string $pattern
     * @return array
     */
    public function glob($pattern);

    /**
     * Equivalent to PHP `is_file` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isFile($path);

    /**
     * Equivalent to PHP `is_dir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isDir($path);


    /**
     * Equivalent to PHP `file_exists` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function fileExists($path);

    /**
     * Equivalent to PHP `md5_file` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function md5File($path);

    /**
     * Equivalent to PHP `file_get_contents` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function fileGetContents($path);

    /**
     * Equivalent to PHP `file_put_contents` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function filePutContents($path, $content, $flags = 0);

    /**
     * Equivalent to PHP `is_readable` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isReadable($path);

    /**
     * Equivalent to PHP `is_writable` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isWritable($path);

    /**
     * Equivalent to PHP `is_executable` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isExecutable($path);

    /**
     * Equivalent to PHP `unlink` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function unlink($path);

    /**
     * Equivalent to PHP `rmdir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function rmdir($path);

    /**
     * Equivalent to PHP `rename` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function rename($oldPath, $newPath);

    /**
     * Equivalent to PHP `fileperms` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function fileperms($path);

    /**
     * Equivalent to PHP `chmod` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function chmod($path, $value);

    /**
     * Equivalent to PHP `clearstatcache` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function clearstatcache($clearRealCache, $path);

    /**
     * Equivalent to PHP `mkdir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function mkdir($path, $mode = 0777, $recursive = false);

    /**
     * Creates a new empty file, overrite.
     *
     * @param  string  $path
     * @return boolean
     */
    public function createFile($path);

    /**
     * Gets the extension of `$path`.
     *
     * @param  string  $path
     * @return boolean
     */
    public function extension($path);

    /**
     * Equivalent to PHP `basename` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function basename($path);

}
