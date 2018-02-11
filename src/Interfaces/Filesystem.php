<?php namespace Tarsana\Filesystem\Interfaces;

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
     * Gets the filesystem adapter.
     *
     * @return Tarsana\Filesystem\Interfaces\Adapter
     */
    public function adapter();

    /**
     * Tells what is the given pattern matching, returns 'file' or 'dir' if a
     * single file or directory matches the pattern. Returns 'collection'
     * if there are multiple matches and 'nothing' if no match found.
     *
     * @param  string  $pattern
     * @return string
     */
    public function whatIs($pattern);

    /**
     * Checks if the given path is a file.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isFile($path);

    /**
     * Checks if all the given path are files.
     *
     * @param  array   $paths
     * @return boolean
     */
    public function areFiles($paths);

    /**
     * Checks if the given path is a directory.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isDir($path);

    /**
     * Checks if all the given paths are directories.
     *
     * @param  array   $paths
     * @return boolean
     */
    public function areDirs($paths);

    /**
     * Checks if the given path is a file or directory.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isAny($path);

    /**
     * Checks if all the given paths are files or directories.
     *
     * @param  array   $paths
     * @return boolean
     */
    public function areAny($paths);

    /**
     * Checks if the given path is readable.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isReadable($path);

    /**
     * Checks if all the given paths are readable.
     *
     * @param  string  $paths
     * @return boolean
     */
    public function areReadable($paths);

    /**
     * Checks if the given path is writable.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isWritable($path);

    /**
     * Checks if all the given paths are writable.
     *
     * @param  string  $paths
     * @return boolean
     */
    public function areWritable($paths);

    /**
     * Checks if the given path is executable.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isExecutable($path);

    /**
     * Checks if all the given paths are executable.
     *
     * @param  string  $paths
     * @return boolean
     */
    public function areExecutable($paths);

    /**
     * Gets a file by relative or absolute path,
     * optionally creates the file if missing.
     *
     * @param  string  $path
     * @param  boolean $createMissing
     * @return Tarsana\Filesystem\Interfaces\File
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException
     */
    public function file($path, $createMissing = false);

    /**
     * Gets files by relative or absolute path,
     * optionally creates missing files.
     *
     * @param  array   $paths
     * @param  boolean $createMissing
     * @return Tarsana\Filesystem\Interfaces\Collection
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException
     */
    public function files($paths = false, $createMissing = false);

    /**
     * Gets a directory by relative or absolute path,
     * optionally creates the directory if missing.
     *
     * @param  string  $path
     * @param  boolean $createMissing
     * @return Tarsana\Filesystem\Interfaces\Directory
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException
     */
    public function dir($path, $createMissing = false);

    /**
     * Gets directories by relative or absolute path,
     * optionally creates missing directories.
     *
     * @param  array   $paths
     * @param  boolean $createMissing
     * @return Tarsana\Filesystem\Interfaces\Collection
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException
     */
    public function dirs($paths = false, $createMissing = false);

    /**
     * Finds files and directories matching the given pattern
     * and returns a collection containing them.
     *
     * @param  string  $pattern
     * @return Tarsana\Filesystem\Interfaces\Collection
     */
    public function find($pattern);

    /**
     * Removes a file or directory recursively.
     *
     * @param  string  $path
     * @return Tarsana\Filesystem\Interfaces
     */
    public function remove($path);

    /**
     * Removes an array of files or directories.
     *
     * @param  array   $paths
     * @return Tarsana\Filesystem\Interfaces
     */
    public function removeAll($paths);

}
