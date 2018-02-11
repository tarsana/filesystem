<?php namespace Tarsana\Filesystem\Interfaces;


interface File extends AbstractFile {

    /**
     * Gets a hash of the file/directory content.
     *
     * @return string
     */
    public function hash();

    /**
     * Gets or sets the content of the file.
     *
     * @param  string $content
     * @return string|Tarsana\Filesystem\File
     */
    public function content($content = false);

    /**
     * Appends a content to the file.
     *
     * @param  string $content
     * @return Tarsana\Filesystem\File
     *
     * @throws Tarsana\Filesystem\Exceptions\FilesystemException if unable to append the content.
     */
    public function append($content);

    /**
     * Gets or sets the file extension.
     *
     * @param  string $extension
     * @return string|Tarsana\Filesystem\File
     */

    public function extension($extension = false);

    /**
     * Returns `true` if the file is writable, `false` otherwise.
     *
     * @return boolean
     */
    public function isWritable();

    /**
     * Returns `true` if the file is executable, `false` otherwise.
     *
     * @return boolean
     */
    public function isExecutable();

}
