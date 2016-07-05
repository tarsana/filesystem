<?php namespace Tarsana\IO\Interfaces;

/**
 * Writes data to a stream.
 */
interface WriterInterface {
    /**
     * Writes content.
     *
     * @return self
     */
    public function write($content);

    /**
     * Writes content and adds EOL.
     *
     * @return self
     */
    public function writeLine($content);

}
