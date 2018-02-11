<?php namespace Tarsana\Filesystem;


use Tarsana\Filesystem\Filesystem;
use Tarsana\Filesystem\Adapters\Local;
use Tarsana\Filesystem\Interfaces\Adapter;
use Tarsana\Filesystem\Exceptions\FilesystemException;
use Tarsana\Filesystem\Interfaces\AbstractFile as AbstractFileInterface;


abstract class AbstractFile implements AbstractFileInterface {

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
     *
     * @var Tarsana\Filesystem\Interfaces
     */
    protected $fs;

    /**
     * The filesystem adapter. It defines the low-level functions
     * like (fopen, mkdir, ...) for the targeted filesystem.
     *
     * @var Tarsana\Filesystem\Interfaces\Adapter
     */
    protected $adapter;

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
    public function __construct($path, Adapter $adapter = null)
    {
        $this->adapter = (null !== $adapter) ? $adapter : Local::instance();
        $this->path = $path;
        $this->pathListeners = [];
        $this->fs = null; // will create it when needed

        if (! $this->canCreate()) {
            $this->throwUnableToCreate();
        }
        $this->create();
        $this->path = $this->adapter->realpath($this->path);
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
        return $this->exists() || ! $this->adapter->fileExists($this->path);
    }

    /**
     * Gets or sets the name of the file.
     *
     * @param  string $value
     * @param  boolean $overwrite
     * @return string|Tarsana\Filesystem\AbstractFile
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException if invalid name given or could not rename the file.
     */
    public function name($value = false, $overwrite = false)
    {
        if ($value === false) {
            return $this->adapter->basename($this->path);
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
     * @return string|Tarsana\Filesystem\AbstractFile
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException if could not rename the file.
     */
    public function path($value = false, $overwrite = false)
    {
        if ($value === false) {
            return $this->path;
        }

        $oldPath = $this->path;

        if (!$overwrite && $this->adapter->fileExists($value)) {
            throw new FilesystemException("Cannot rename the file '{$this->path}' to '{$value}' because a file already exists");
        }

        (new static($value, $this->adapter))->remove();

        if (! $this->adapter->rename($this->path, $value)) {
            throw new FilesystemException("Cannot rename the file '{$this->path}' to '{$value}'");
        }

        $this->path = $value;
        $this->fs = null;
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
     * @return string|Tarsana\Filesystem\AbstractFile
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException if could not apply permissions to file.
     */
    public function perms($value = false)
    {
        if ($value === false) {
            return substr(sprintf('%o', $this->adapter->fileperms($this->path)), -4);
        }

        if (! $this->adapter->chmod($this->path, $value)) {
            throw new FilesystemException("Unable to apply permissions to '{$this->path}'");
        }

        $this->clearStat();

        return $this;
    }

    /**
     * Gets the filesystem coresponding to the file.
     *
     * @return Tarsana\Filesystem
     */
    public function fs()
    {
        if (null === $this->fs)
            $this->fs = new Filesystem($this->getFilesystemPath(), $this->adapter);
        return $this->fs;
    }

    /**
     * Removes the file.
     *
     * @return void
     */
    public function remove()
    {
        $this->fs()->remove($this->path, true);
    }

    /**
     * Clears stat cached informations.
     *
     * @return  void
     */
    protected function clearStat()
    {
        $this->adapter->clearstatcache(true, $this->path);
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
     * @return Tarsana\Filesystem\AbstractFile
     */
    public abstract function create();

    /**
     * Throws a FilesystemException meaning that
     * it were not possible to create the file.
     *
     * @return Tarsana\Filesystem\AbstractFile
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException
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
     * @return Tarsana\Filesystem\AbstractFile
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException if unable to create the destination file.
     */
    public abstract function copyAs($dest);

}
