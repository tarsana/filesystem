<?php namespace Tarsana\IO;

use Tarsana\IO\Exceptions\FilesystemException;
use Tarsana\IO\Filesystem\Collection;
use Tarsana\IO\Filesystem\Directory;
use Tarsana\IO\Filesystem\File;


class Filesystem {

    /**
     * The root path of the filesystem.
     * 
     * @var array
     */
    protected $rootPath;

    /**
     * Creates a new Filesystem instance.
     * 
     * @param string $rootPath
     * @throws FilesystemException If root path is not a directory.
     **/
    public function __construct($rootPath)
    {
        if (! $this->isDir($rootPath, true)) {
            throw new FilesystemException("Cannot find the directory '{$rootPath}'");
        }
        // Ensure that the path termiates with '/'
        $rootPath = rtrim($rootPath, '/') . '/';
        $this->rootPath = $rootPath;
    }

    /**
     * Tells what is the given pattern matching, returns 'file' or 'dir' if a 
     * single file or directory matches the pattern. Returns 'collection' 
     * if there are multiple matches and 'nothing' if no match found.
     * 
     * @param  string  $pattern
     * @param  boolean $isAbsolute
     * @return string
     */
    public function whatIs($pattern, $isAbsolute = false)
    {
        if (! $isAbsolute) {
            $pattern = $this->rootPath . $pattern;
        }

        $paths = glob($pattern);

        if (count($paths) == 0)
            return 'nothing';
        if (count($paths) == 1)
            return (is_file($paths[0])) ? 'file' : 'dir';
        return 'collection';
    }

    /**
     * Checks if the given path is of the given type.
     * 
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @param  string  $type
     * @return boolean
     */
    protected function is($path, $isAbsolute, $type)
    {
        if (! $isAbsolute) {
            $path = $this->rootPath . $path;
        }
        switch ($type) {
            case 'file':
                return is_file($path);
            break;
            case 'dir':
                return is_dir($path);
            break;
            case 'any':
                return file_exists($path);
            break;
            default:
                throw new FilesystemException("Unknown file type '{$type}'");
        }
    }

    /**
     * Checks if the given paths are all of the given type.
     * 
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @param  string  $type
     * @return boolean
     */
    protected function are($paths, $areAbsolute, $type)
    {
        foreach ($paths as $path) {
            if (! $this->is($path, $areAbsolute, $type)) {
                return false;
            }
        }
        return (count($paths) == 0) ? false : true;
    }

    /**
     * Checks if the given path is a file.
     * 
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @return boolean
     */
    public function isFile($path, $isAbsolute = false)
    {
        return $this->is($path, $isAbsolute, 'file');
    }

    /**
     * Checks if all the given path are files.
     * 
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @return boolean
     */
    public function areFiles($paths, $areAbsolute = false)
    {
        return $this->are($paths, $areAbsolute, 'file');
    }

    /**
     * Checks if the given path is a directory.
     * 
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @return boolean
     */
    public function isDir($path, $isAbsolute = false)
    {
        return $this->is($path, $isAbsolute, 'dir');
    }

    /**
     * Checks if all the given paths are directories.
     * 
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @return boolean
     */
    public function areDirs($paths, $areAbsolute = false)
    {
        return $this->are($paths, $areAbsolute, 'dir');
    }

    /**
     * Checks if the given path is a file or directory.
     * 
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @return boolean
     */
    public function isAny($path, $isAbsolute = false)
    {
        return $this->is($path, $isAbsolute, 'any');
    }

    /**
     * Checks if all the given paths are files or directories.
     * 
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @return boolean
     */
    public function areAny($paths, $areAbsolute = false)
    {
        return $this->are($paths, $areAbsolute, 'any');
    }

    /**
     * Gets a file by relative or absolute path, 
     * optionally creates the file if missing.
     * 
     * @param  string  $path
     * @param  boolean $createMissing
     * @param  boolean $isAbsolute
     * @return Tarsana\IO\Filesystem\File
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function file($path, $createMissing = false, $isAbsolute = false)
    {
        if (! $isAbsolute) {
            $path = $this->rootPath . $path;
        }
        if (! $createMissing && ! $this->isFile($path, true)) {
            throw new FilesystemException("Cannot find the file '{$path}'");
        }
        return new File($path);
    }

    /**
     * Gets files by relative or absolute path, 
     * optionally creates missing files.
     * 
     * @param  array   $paths
     * @param  boolean $createMissing
     * @return Tarsana\IO\Filesystem\File
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
     * @param  boolean $isAbsolute
     * @return Tarsana\IO\Filesystem\File
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    public function dir($path, $createMissing = false, $isAbsolute = false)
    {
        if (! $isAbsolute) {
            $path = $this->rootPath . $path;
        }
        if (! $createMissing && ! $this->isDir($path, true)) {
            throw new FilesystemException("Cannot find the directory '{$path}'");
        }
        return new Directory($path);
    }

    /**
     * Gets directories by relative or absolute path, 
     * optionally creates missing directories.
     * 
     * @param  array   $paths
     * @param  boolean $createMissing
     * @return Tarsana\IO\Filesystem\File
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
     * @param  boolean $isAbsolute
     * @return Collection
     */
    public function find($pattern, $isAbsolute = false)
    {
        if (! $isAbsolute) {
            $pattern = $this->rootPath . $pattern;
        }
        $list = new Collection;
        foreach (glob($pattern) as $path) {
            if ($this->isFile($path, true)) {
                $list->add(new File($path));
            } else {
                $list->add(new Directory($path));
            }
        }
        return $list;
    }

    /**
     * Removes a file or directory recursively.
     * @param  string  $path
     * @param  boolean $isAbsolute
     * @return Tarsana\IO\Filesystem
     */
    public function remove($path, $isAbsolute = false)
    {
        if (! $isAbsolute) {
            $path = $this->rootPath . $path;
        }
        if ($this->isFile($path, true)) {
            unlink($path);        
        } else {
            // clean the directory
            $path = rtrim($path, '/') . '/';
            foreach (glob($path . '*') as $itemPath) {
                $this->remove($itemPath, true);
            }
            // remove it
            rmdir($path);
        }
        return $this;
    }

    /**
     * Removes an array of files or directories.
     * 
     * @param  array   $paths
     * @param  boolean $areAbsolute
     * @return Tarsana\IO\Filesystem
     */
    public function removeAll($paths, $areAbsolute = false)
    {
        foreach ($paths as $path) {
            $this->remove($path, $areAbsolute);
        }
        return $this;
    }

}