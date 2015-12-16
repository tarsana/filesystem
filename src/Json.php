<?php namespace Tarsana\IO;

use Tarsana\IO\Exceptions\DumperException;
use Tarsana\IO\Exceptions\ParserException;
use Tarsana\IO\Interfaces\Dumper;
use Tarsana\IO\Interfaces\Parser;
use Tarsana\IO\Helper;


/**
 * Encodes and decodes JSON.
 */
class Json implements Parser, Dumper {
    /**
     * Decodes a string and returns the resulting data.
     *
     * @param  string $str
     * @param  array  $config
     * @return array
     *
     * @throws Tarsana\IO\Exceptions\ParserException
     */
    public static function parse($str, $config = [])
    {
        $data = json_decode($str);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ParserException("Error while parsing JSON");
        }

        return $data;
    }

    /**
     * Encodes data as a JSON string.
     * 
     * @param  mixed $data
     * @param  array $config
     * @return string
     *
     * @throws Tarsana\IO\Exceptions\DumperException
     */
    public static function dump($data, $config = [])
    {
        $json = json_encode(Helper::toArray($data));

        if($json === false){
            throw new DumperException("Cannot encode the data to JSON");
        }

        return $json;
    }

}
