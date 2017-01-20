<?php namespace Tarsana\IO\Interfaces\Filesystem;

use Tarsana\IO\Exceptions\FilesystemException;
use Tarsana\IO\Filesystem\AbstractFile;


class File extends AbstractFile {

    /**
     * Tells if the file exists.
     *
     * @return boolean
     */
    public function exists()
    {
        return is_file($this->path);
    }

    /**
     * Creates the file if it doesn't exist.
     *
     * @return Tarsana\IO\Filesystem\File
     */
    public function create()
    {
        if (! file_exists($this->path)) {
            // ensure the parent directory exists
            new Directory(dirname($this->path));

            $file = fopen($this->path, "w");
            if ($file === false) {
                $this->throwUnableToCreate();
            }
            fclose($file);
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
     * @return Tarsana\IO\Filesystem\File
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if unable to create the destination file.
     */
    public function copyAs($dest)
    {
        $copy = new File($dest);
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
        return md5_file($this->path);
    }

    /**
     * Gets or sets the content of the file.
     *
     * @param  string $content
     * @return string|Tarsana\IO\Filesystem\File
     */
    public function content($content = false)
    {
        if ($content === false) {
            return file_get_contents($this->path);
        }

        file_put_contents($this->path, $content);
        return $this;
    }

    /**
     * Appends a content to the file.
     *
     * @param  string $content
     * @return Tarsana\IO\Filesystem\File
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if unable to append the content.
     */
    public function append($content)
    {
        if (file_put_contents($this->path, $content, FILE_APPEND) === false) {
            throw new FilesystemException("Cannot append content to the file '{$this->path}'");
        }
        return $this;
    }

    /**
     * Gets or sets the file extension.
     *
     * @param  string $extension
     * @return string|Tarsana\IO\Filesystem\File
     */
    public function extension($extension = false)
    {
        if($extension === false) {
            return pathinfo($this->path, PATHINFO_EXTENSION);
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
        return is_writable($this->path);
    }

    /**
     * Returns TRUE if the file is executable, FALSE otherwise.
     *
     * @return boolean
     */
    public function isExecutable()
    {
        return is_executable($this->path);
    }
}
