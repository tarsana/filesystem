<?php namespace Tarsana\IO\Interfaces;

/**
 * Convets some data to a string using a syntax like JSON or YAML.
 */
interface Dumper {
    /**
     * Encodes data as a string.
     * 
     * @param  mixed $data
     * @param  array $config
     * @return string
     */
    public static function dump($data, $config);
}
