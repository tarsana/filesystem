<?php namespace Tarsana\IO;

use Tarsana\IO\Filesystem\File;
use Tarsana\IO\Filesystem\Directory;
use Tarsana\IO\Filesystem\Collection;
use Tarsana\IO\Filesystem\Adapters\Local;
use Tarsana\IO\Interfaces\Filesystem\Adapter;
use Tarsana\IO\Exceptions\FilesystemException;
use Tarsana\IO\Interfaces\Filesystem as FilesystemInterface;

/**
 * Finds and handles files and directories within a root directory.
 */
class Filesystem implements FilesystemInterface {

    /**
     * The absolute root path of the filesystem.
     *
     * @var string
     */
    protected $rootPath;

    /**
     * The adapter filesystem.
     *
     * @var Tarsana\IO\Interfaces\Filesystem\Adapter
     */
    protected $adapter;

    /**
     * Creates a new Filesystem instance from a path and an optional adapter.
     *
     * @param string $rootPath
     * @param Tarsana\IO\Interfaces\Filesystem\Adapter $adapter
     * @throws FilesystemException If root path is not a directory.
     **/
    public function __construct($rootPath, Adapter $adapter = null)
    {
        if (null === $adapter) {
            $adapter = Local::instance();
        }
        $this->adapter = $adapter;

        if (! $this->isDir($rootPath, true)) {
            throw new FilesystemException("Cannot find the directory '{$rootPath}'");
        }
        $this->rootPath = $adapter->realpath($rootPath) . '/';
    }

    /**
     * Gets the root path.
     *
     * @return string
     */
    public function path()
    {
        return $this->rootPath;
    }

    /**
     * Gets the filesystem adapter.
     *
     * @return Tarsana\IO\Interfaces\Filesystem\Adapter
     */
    public function adapter()
    {
        return $this->adapter;
    }

    /**
     * Tells what is the given pattern matching, returns 'file' or 'dir' if a
     * single file or directory matches the pattern. Returns 'collection'
     * if there are multiple matches and 'nothing' if no match found.
     *
     * @param  string  $pattern
     * @return string
     */
    public function whatIs($pattern)
    {
        if (! $this->adapter->isAbsolute($pattern)) {
            $pattern = $this->rootPath . $pattern;
        }

        $paths = $this->adapter->glob($pattern);

        if (count($paths) == 0)
            return 'nothing';
        if (count($paths) == 1)
            return ($this->adapter->isFile($paths[0])) ? 'file' : 'dir';
        return 'collection';
    }

    /**
     * Checks if the given path is of the given type.
     *
     * @param  string  $path
     * @param  string  $type
     * @return boolean
     */
    protected function is($path, $type)
    {
        if (! $this->adapter->isAbsolute($path)) {
            $path = $this->rootPath . $path;
        }
        switch ($type) {
            case 'readable':
                return $this->adapter->isReadable($path);
            case 'writable':
                return $this->adapter->isWritable($path);
            case 'executable':
                return $this->adapter->isExecutable($path);
            case 'file':
                return $this->adapter->isFile($path);
            case 'dir':
                return $this->adapter->isDir($path);
            case 'any':
                return $this->adapter->fileExists($path);
            default:
                throw new FilesystemException("Unknown file type '{$type}'");
        }
    }

    /**
     * Checks if the given paths are all of the given type.
     *
     * @param  array   $paths
     * @param  string  $type
     * @return boolean
     */
    protected function are($paths, $type)
    {
        foreach ($paths as $path) {
            if (! $this->is($path, $type)) {
                return false;
            }
        }
        return (count($paths) == 0) ? false : true;
    }

    /**
     * Checks if the given path is a file.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isFile($path)
    {
        return $this->is($path, 'file');
    }

    /**
     * Checks if all the given path are files.
     *
     * @param  array   $paths
     * @return boolean
     */
    public function areFiles($paths)
    {
        return $this->are($paths, 'file');
    }

    /**
     * Checks if the given path is a directory.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isDir($path)
    {
        return $this->is($path, 'dir');
    }

    /**
     * Checks if all the given paths are directories.
     *
     * @param  array   $paths
     * @return boolean
     */
    public function areDirs($paths)
    {
        return $this->are($paths, 'dir');
    }

    /**
     * Checks if the given path is a file or directory.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isAny($path)
    {
        return $this->is($path, 'any');
    }

    /**
     * Checks if all the given paths are files or directories.
     *
     * @param  array   $paths
     * @return boolean
     */
    public function areAny($paths)
    {
        return $this->are($paths, 'any');
    }

    /**
     * Checks if the given path is readable.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isReadable($path)
    {
        return $this->is($path, 'readable');
    }

    /**
     * Checks if all the given paths are readable.
     *
     * @param  string  $paths
     * @return boolean
     */
    public function areReadable($paths)
    {
        return $this->are($paths, 'readable');
    }

    /**
     * Checks if the given path is writable.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isWritable($path)
    {
        return $this->is($path, 'writable');
    }

    /**
     * Checks if all the given paths are writable.
        return $this->are($paths, 'readable');
     *
     * @param  string  $paths
     * @return boolean
     */
    public function areWritable($paths)
    {
        return $this->are($paths, 'writable');
    }

    /**
     * Checks if the given path is executable.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isExecutable($path)
    {
        return $this->is($path, 'executable');
    }

    /**
     * Checks if all the given paths are executable.
     *
     * @param  string  $paths
     * @return boolean
     */
    public function areExecutable($paths)
    {
        return $this->are($paths, 'executable');
    }

    /**
     * Gets a file by relative or absolute path,
     * optionally creates the file if missing.
     *
     * @param  string  $path
     * @param  boolean $createMissing
     * @return Tarsana\IO\Filesystem\File
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function file($path, $createMissing = false)
    {
        if (! $this->adapter->isAbsolute($path)) {
            $path = $this->rootPath . $path;
        }
        if (! $createMissing && ! $this->isFile($path, true)) {
            throw new FilesystemException("Cannot find the file '{$path}'");
        }

        return new File($path, $this->adapter);
    }

    /**
     * Gets files by relative or absolute path,
     * optionally creates missing files.
     *
     * @param  array   $paths
     * @param  boolean $createMissing
     * @return Tarsana\IO\Filesystem\Collection
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function files($paths = false, $createMissing = false)
    {
        if ($paths === false) {
            return $this->find('*')->files();
        }

        $list = new Collection;
        foreach ($paths as $path) {
            $list->add($this->file($path, $createMissing));
        }
        return $list;
    }

    /**
     * Gets a directory by relative or absolute path,
     * optionally creates the directory if missing.
     *
     * @param  string  $path
     * @param  boolean $createMissing
     * @return Tarsana\IO\Filesystem\Directory
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function dir($path, $createMissing = false)
    {
        if (! $this->adapter->isAbsolute($path)) {
            $path = $this->rootPath . $path;
        }
        if (! $createMissing && ! $this->isDir($path, true)) {
            throw new FilesystemException("Cannot find the directory '{$path}'");
        }
        return new Directory($path, $this->adapter);
    }

    /**
     * Gets directories by relative or absolute path,
     * optionally creates missing directories.
     *
     * @param  array   $paths
     * @param  boolean $createMissing
     * @return Tarsana\IO\Filesystem\Collection
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function dirs($paths = false, $createMissing = false)
    {
        if ($paths === false) {
            return $this->find('*')->dirs();
        }

        $list = new Collection;
        foreach ($paths as $path) {
            $list->add($this->dir($path, $createMissing));
        }
        return $list;
    }

    /**
     * Finds files and directories matching the given pattern
     * and returns a collection containing them.
     *
     * @param  string  $pattern
     * @return Tarsana\IO\Filesystem\Collection
     */
    public function find($pattern)
    {
        if (! $this->adapter->isAbsolute($pattern)) {
            $pattern = $this->rootPath . $pattern;
        }
        $list = new Collection;
        foreach ($this->adapter->glob($pattern) as $path) {
            if ($this->isFile($path, true)) {
                $list->add(new File($path, $this->adapter));
            } else {
                $list->add(new Directory($path, $this->adapter));
            }
        }
        return $list;
    }

    /**
     * Removes a file or directory recursively.
     *
     * @param  string  $path
     * @return Tarsana\IO\Filesystem
     */
    public function remove($path)
    {
        if (! $this->adapter->isAbsolute($path)) {
            $path = $this->rootPath . $path;
        }
        if ($this->isFile($path, true)) {
            $this->adapter->unlink($path);
        } else {
            // clean the directory
            $path = rtrim($path, '/') . '/';
            foreach ($this->adapter->glob($path . '*') as $itemPath) {
                $this->remove($itemPath, true);
            }
            // remove it
            $this->adapter->rmdir($path);
        }
        return $this;
    }

    /**
     * Removes an array of files or directories.
     *
     * @param  array   $paths
     * @return Tarsana\IO\Filesystem
     */
    public function removeAll($paths)
    {
        foreach ($paths as $path) {
            $this->remove($path);
        }
        return $this;
    }

}
