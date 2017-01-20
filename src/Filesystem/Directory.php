<?php namespace Tarsana\IO\Filesystem;

use Tarsana\IO\Interfaces\Filesystem\Directory as DirectoryInterface;
use Tarsana\IO\Exceptions\FilesystemException;
use Tarsana\IO\Filesystem\AbstractFile;


class Directory extends AbstractFile implements DirectoryInterface {

    /**
     * Tells if the directory exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return $this->adapter->isDir($this->path);
    }

    /**
     * Creates the directory if it doesn't exist.
     *
     * @return Tarsana\IO\Filesystem\Directory
     */
    public function create()
    {
        if (! $this->exists() && $this->canCreate()) {
            $this->adapter->mkdir($this->path, 0755, true);
        }
        return $this;
    }

    /**
     * Throws a FilesystemException meaning that
     * it were not possible to create the file.
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException
     */
    protected function throwUnableToCreate()
    {
        throw new FilesystemException("Unable to create the directory '{$this->path}'. A file with the same path already exists.");
    }

    /**
     * Gets the path of the filesystem which is the same as the directory.
     *
     * @return string
     */
    protected function getFilesystemPath()
    {
        return $this->path;
    }

    /**
     * Copies the directory to the provided destination and returns the copy.
     *
     * @param  string $dest
     * @return Tarsana\IO\Filesystem\Directory
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if unable to create the destination directory.
     */
    public function copyAs($dest)
    {
        $dest = rtrim($dest, '/') . '/';

        $copy = new Directory($dest, $this->adapter); // create the destination directory

        foreach ($this->fs()->find('*')->asArray() as $file) {
            $file->copyAs($dest . $file->name());
        }

        return $copy;
    }

}
