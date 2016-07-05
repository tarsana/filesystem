<?php namespace Tarsana\IO\Interfaces;

/**
 * Reads from a stream of data.
 */
interface ReaderInterface {
    /**
     * Reads content.
     *
     * @param  int $length the max length to read, -1 specifies no limit.
     * @return string
     */
    public function read ($length = -1);

    /**
     * Reads one line.
     *
     * @return string
     */
    public function readLine ();

    /**
     * Pipe all the content to the given writer.
     *
     * @param  WriterInterface $w
     * @return void
     */
    public function pipe(WriterInterface $w);

}
