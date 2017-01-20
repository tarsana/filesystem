<?php namespace Tarsana\IO\Filesystem;

use Tarsana\IO\Interfaces\Filesystem\File as FileInterface;
use Tarsana\IO\Exceptions\FilesystemException;
use Tarsana\IO\Filesystem\AbstractFile;


class File extends AbstractFile implements FileInterface {

    /**
     * Tells if the file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return $this->adapter->isFile($this->path, true);
    }

    /**
     * Creates the file if it doesn't exist.
     *
     * @return self
     */
    public function create()
    {
        if (! $this->adapter->fileExists($this->path, true)) {
            // ensure the parent directory exists
            new Directory(dirname($this->path), $this->adapter);

            if ($this->adapter->createFile($this->path) === false) {
                $this->throwUnableToCreate();
            }
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
        throw new FilesystemException("Unable to create the file '{$this->path}'. A directory with the same path already exists.");
    }

    /**
     * Gets the path of the parent directory of the file.
     *
     * @return string
     */
    protected function getFilesystemPath()
    {
        return dirname($this->path);
    }

    /**
     * Copies the file to the provided destination and returns the copy.
     *
     * @param  string $dest
     * @return self
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if unable to create the destination file.
     */
    public function copyAs($dest)
    {
        $copy = new File($dest, $this->adapter);
        $copy->content($this->content());
        return $copy;
    }

    /**
     * Gets a hash of the file/directory content.
     *
     * @return string
     */
    public function hash()
    {
        return $this->adapter->md5File($this->path);
    }

    /**
     * Gets or sets the content of the file.
     *
     * @param  string $content
     * @return string|self
     */
    public function content($content = false)
    {
        if ($content === false) {
            return $this->adapter->fileGetContents($this->path);
        }

        $this->adapter->filePutContents($this->path, $content);
        return $this;
    }

    /**
     * Appends a content to the file.
     *
     * @param  string $content
     * @return self
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if unable to append the content.
     */
    public function append($content)
    {
        if ($this->adapter->filePutContents($this->path, $content, FILE_APPEND) === false) {
            throw new FilesystemException("Cannot append content to the file '{$this->path}'");
        }
        return $this;
    }

    /**
     * Gets or sets the file extension.
     *
     * @param  string $extension
     * @return string|self
     */
    public function extension($extension = false)
    {
        if($extension === false) {
            return $this->adapter->extension($this->path);
        }

        $newName = $this->name();
        $index = strrpos($newName, '.');
        if ($index !== false) {
            $newName = substr($newName, 0, $index + 1);
        } else {
            $newName .= '.';
        }
        $newName .= $extension;

        return $this->name($newName);
    }

    /**
     * Returns TRUE if the file is writable, FALSE otherwise.
     *
     * @return boolean
     */
    public function isWritable()
    {
        return $this->adapter->isWritable($this->path);
    }

    /**
     * Returns TRUE if the file is executable, FALSE otherwise.
     *
     * @return boolean
     */
    public function isExecutable()
    {
        return $this->adapter->isExecutable($this->path);
    }
}
