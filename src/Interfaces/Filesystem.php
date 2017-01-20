<?php namespace Tarsana\IO\Interfaces;

/**
 * Finds and handles files and directories within a root directory.
 */
interface Filesystem {

    /**
     * Gets the root path.
     *
     * @return string
     */
    public function path();

    /**
     * Tells what is the given pattern matching, returns 'file' or 'dir' if a
     * single file or directory matches the pattern. Returns 'collection'
     * if there are multiple matches and 'nothing' if no match found.
     *
     * @param  string  $pattern
     * @param  boolean $isAbsolute
     * @return string
     */
    public function whatIs($pattern, $isAbsolute = false);

    /**
     * Checks if the given path is a file.
     *
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @return boolean
     */
    public function isFile($path, $isAbsolute = false);

    /**
     * Checks if all the given path are files.
     *
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @return boolean
     */
    public function areFiles($paths, $areAbsolute = false);

    /**
     * Checks if the given path is a directory.
     *
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @return boolean
     */
    public function isDir($path, $isAbsolute = false);

    /**
     * Checks if all the given paths are directories.
     *
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @return boolean
     */
    public function areDirs($paths, $areAbsolute = false);

    /**
     * Checks if the given path is a file or directory.
     *
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @return boolean
     */
    public function isAny($path, $isAbsolute = false);

    /**
     * Checks if all the given paths are files or directories.
     *
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @return boolean
     */
    public function areAny($paths, $areAbsolute = false);

    /**
     * Gets a file by relative or absolute path,
     * optionally creates the file if missing.
     *
     * @param  string  $path
     * @param  boolean $createMissing
     * @param  boolean $isAbsolute
     * @return Tarsana\IO\Interfaces\Filesystem\File
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function file($path, $createMissing = false, $isAbsolute = false);

    /**
     * Gets files by relative or absolute path,
     * optionally creates missing files.
     *
     * @param  array   $paths
     * @param  boolean $createMissing
     * @return Tarsana\IO\Interfaces\Filesystem\Collection
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function files($paths = false, $createMissing = false);

    /**
     * Gets a directory by relative or absolute path,
     * optionally creates the directory if missing.
     *
     * @param  string  $path
     * @param  boolean $createMissing
     * @param  boolean $isAbsolute
     * @return Tarsana\IO\Interfaces\Filesystem\Directory
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function dir($path, $createMissing = false, $isAbsolute = false);

    /**
     * Gets directories by relative or absolute path,
     * optionally creates missing directories.
     *
     * @param  array   $paths
     * @param  boolean $createMissing
     * @return Tarsana\IO\Interfaces\Filesystem\Collection
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function dirs($paths = false, $createMissing = false);

    /**
     * Finds files and directories matching the given pattern
     * and returns a collection containing them.
     *
     * @param  string  $pattern
     * @param  boolean $isAbsolute
     * @return Tarsana\IO\Interfaces\Filesystem\Collection
     */
    public function find($pattern, $isAbsolute = false);

    /**
     * Removes a file or directory recursively.
     *
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @return Tarsana\IO\Interfaces\Filesystem
     */
    public function remove($path, $isAbsolute = false);

    /**
     * Removes an array of files or directories.
     *
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @return Tarsana\IO\Interfaces\Filesystem
     */
    public function removeAll($paths, $areAbsolute = false);

}
