<?php namespace Tarsana\IO\Interfaces\Filesystem;

interface AbstractFile {

    /**
     * Tells if the file exists or can be created. In case of a directory
     * for example, this will return false if a file exists with the
     * same path which makes it impossible o create a directory
     *
     * @return boolean
     */
    public function canCreate();

    /**
     * Gets or sets the name of the file.
     *
     * @param  string $value
     * @param  boolean $overwrite
     * @return string|self
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if invalid name given or could not rename the file.
     */
    public function name($value = false, $overwrite = false);

    /**
     * Gets or Sets the path.
     *
     * @param  string $value
     * @param  boolean $overwrite
     * @return string|self
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if could not rename the file.
     */
    public function path($value = false, $overwrite = false);

    /**
     * Adds new path listener which should accept two
     * parameters: the old path and the new one.
     * It returns the index of the listener.
     *
     * @param callable $callback
     * @return int
     */
    public function addPathListener($callback);

    /**
     * Removes a path listener by index.
     *
     * @param  int $index
     * @return void
     */
    public function removePathListener($index);

    /**
     * Gets or Sets the file permissions.
     *
     * @param  int $value
     * @return string|self
     *
     * @throws Tarsana\IO\Exceptions\FilesystemException if could not apply permissions to file.
     */
    public function perms($value = false);

    /**
     * Gets the filesystem coresponding to the file.
     *
     * @return Tarsana\IO\Interface\Filesystem
     */
    public function fs();

    /**
     * Removes the file.
     *
     * @return void
     */
    public function remove();

}
