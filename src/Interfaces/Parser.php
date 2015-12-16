<?php namespace Tarsana\IO\Interfaces;

/**
 * Converts a string to an array using a syntax like JSON or YAML.
 */
interface Parser {
    /**
     * Decodes a string and returns the corresponding array.
     *
     * @param  string $str     The string to decode.
     * @param  string $config  Additional configuration for the parser.
     * @return array
     */
    public static function parse($str, $config);
}
