<?php namespace Tarsana\Filesystem\Interfaces\Resource;

/**
 * Writes content into a text resource.
 */
interface Writer {

    /**
     * Writes content.
     *
     * @param  string $content
     * @return self
     */
    public function write($content);

    /**
     * Writes the given text then an end of line character.
     *
     * @param  string $text
     * @return self
     */
    public function writeLine($text);

}
