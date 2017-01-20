<?php namespace Tarsana\IO\Interfaces\Resource;

/**
 * Writes content into a stream of data.
 */
interface Writer {

    /**
     * Writes content.
     *
     * @param  string $content
     * @return self
     */
    public function write($content);

}
