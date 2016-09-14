<?php namespace Tarsana\IO\Interfaces;

/**
 * Writes content into a stream of data.
 */
interface WriterInterface {

    /**
     * Writes content.
     *
     * @param  string $content
     * @return self
     */
    public function write($content);

}
