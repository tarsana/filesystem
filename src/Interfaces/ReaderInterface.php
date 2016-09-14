<?php namespace Tarsana\IO\Interfaces;

/**
 * Reads content from a stream of data.
 */
interface ReaderInterface {

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
}
