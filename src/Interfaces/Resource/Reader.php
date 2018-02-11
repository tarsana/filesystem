<?php namespace Tarsana\Filesystem\Interfaces\Resource;

/**
 * Reads content from a text resource.
 */
interface Reader {

    /**
     * Reads a number of chars/bytes from the stream.
     * If `$count == 0`; reads all the available content.
     *
     * @param  int $count
     * @return string
     */
    public function read($count = 0);

    /**
     * Sets the blocking mode.
     *
     * @param  bool $isBlocking
     * @return self
     */
    public function blocking($isBlocking);

    /**
     * Reads until end of line or the end of contents.
     * Returns the read string without the end of line.
     *
     * @return string
     */
    public function readLine();

    /**
     * Reads until the given string or the end of contents.
     * Returns the read string without the ending string.
     *
     * @param  string $end
     * @return string
     */
    public function readUntil($end);

}
