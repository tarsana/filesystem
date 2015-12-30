<?php namespace Tarsana\IO\Filesystem;

use Tarsana\IO\Exceptions\FilesystemException;
use Tarsana\IO\Filesystem;


abstract class AbstractFile {

    /**
     * Absolute path to the file.
     * 
     * @var string
     */
    protected $path;

    /**
     * The filesystem instance having the same path 
     * if this is directory and poiting to the 
     * parent directory if this is a file.
     */
    protected $fs;

    /**
     * Functions to call when the file path changes.
     * 
     * @var array
     */
    protected $pathListeners;

    /**
     * Creates a File/Directory instance.
     * 
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = str_replace('/', DIRECTORY_SEPARATOR, $path);
        if (! $this->canCreate()) {
            $this->throwUnableToCreate();
        }
        $this->create();
        $this->fs = new Filesystem($this->getFilesystemPath());

        $this->pathListeners = [];
    }

    /**
     * Tells if the file exists or can be created. In case of a directory 
     * for example, this will return false if a file exists with the 
     * same path which makes it impossible o create a directory
     * 
     * @return boolean
     */
    public function canCreate()
    {
        return ($this->exists() || ! file_exists($this->path));
    }

    /**
     * Gets or sets the name of the file.
     * 
     * @param  string $value
     * @param  boolean $overwrite
     * @return string|Tarsana\IO\Filesystem\AbstractFile
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if invalid name given or could not rename the file.
     */
    public function name($value = false, $overwrite = false)
    {
        if ($value === false) {
            return basename($this->path);
        }
        
        if (! is_string($value) || strlen($value) == 0) {
            throw new FilesystemException('Invalid name given to name() method');
        }

        $newPath = substr($this->path, 0, strrpos($this->path, DIRECTORY_SEPARATOR) + 1);
        $newPath .= $value;

        return $this->path($newPath, $overwrite);
    }

    /**
     * Gets or Sets the path.
     * 
     * @param  string $value
     * @param  boolean $overwrite
     * @return string|Tarsana\IO\Filesystem\AbstractFile
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if could not rename the file.     
     */
    public function path($value = false, $overwrite = false)
    {
        if ($value === false) {
            return $this->path;
        }

        $oldPath = $this->path;

        if (!$overwrite && file_exists($value)) {
            throw new FilesystemException("Cannot rename the file '{$this->path}' to '{$value}' because a file already exists");
        }

        (new static($value))->remove();

        if (! rename($this->path, $value)) {
            throw new FilesystemException("Cannot rename the file '{$this->path}' to '{$value}'");
        }

        $this->path = $value;

        $this->fs = new Filesystem($this->getFilesystemPath());

        $this->pathChanged($oldPath);

        return $this;
    }

    /**
     * Notifies the path listeners and passes the old and
     * the new path as parameters to each callback.
     * 
     * @return void
     */
    protected function pathChanged($oldPath)
    {
        foreach ($this->pathListeners as $cb) {
            $cb($oldPath, $this->path);
        }
    }

    /**
     * Adds new path listener. It should accept two 
     * parameters: the old path and the new one.
     * It returns the index of the listener.
     * 
     * @param Closure $cb
     * @return int
     */
    public function addPathListener($cb)
    {
        $this->pathListeners[] = $cb;
        end($this->pathListeners);
        return key($this->pathListeners);
    }

    /**
     * Removes a path listener by index.
     * 
     * @param  int $index
     * @return void
     */
    public function removePathListener($index)
    {
        if (array_key_exists($index, $this->pathListeners)) {
            unset($this->pathListeners[$index]);
        }
    }

    /**
     * Gets or Sets the file permissions.
     * 
     * @param  int $value
     * @return string|Tarsana\IO\Filesystem\AbstractFile
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if could not apply permissions to file.     
     */
    public function perms($value = false)
    {
        if ($value === false) {
            return substr(sprintf('%o', fileperms($this->path)), -4);
        }

        if (! chmod($this->path, $value)) {
            throw new FilesystemException("Unable to apply permissions to '{$this->path}'");
        }

        $this->clearStat();

        return $this;
    }

    /**
     * Gets the filesystem coresponding to the file.
     * 
     * @return Tarsana\IO\Filesystem
     */
    public function fs()
    {
        return $this->fs;
    }


    /**
     * Removes the file.
     * 
     * @return void
     */
    public function remove()
    {
        $this->fs->remove($this->path, true);
    }

    /**
     * Clears stat cached informations.
     *
     * @return  void
     */
    protected function clearStat()
    {
        clearstatcache(true, $this->path);
    }

    /**
     * Tells if the file exists.
     * 
     * @return boolean
     */
    public abstract function exists();

    /** 
     * Creates the file if it doesn't exist.
     *
     * @return Tarsana\IO\Filesystem\AbstractFile
     */
    public abstract function create();

    /**
     * Throws a FilesystemException meaning that 
     * it were not possible to create the file.
     *
     * @return Tarsana\IO\Filesystem\AbstractFile
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    protected abstract function throwUnableToCreate();

    /**
     * Gets the path of the directory or the parent directory if this is a file.
     * 
     * @return string
     */
    protected abstract function getFilesystemPath();

    /**
     * Copies the file to the provided destination and returns the copy.
     * 
     * @param  string $dest 
     * @return Tarsana\IO\Filesystem\AbstractFile
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if unable to create the destination file.
     */
    public abstract function copyAs($dest);

}
