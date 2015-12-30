<?php namespace Tarsana\IO;

use Symfony\Component\Yaml\Dumper as YamlDumper;
use Symfony\Component\Yaml\Parser as YamlParser;
use Tarsana\IO\Exceptions\DumperException;
use Tarsana\IO\Exceptions\ParserException;
use Tarsana\IO\Interfaces\Dumper;
use Tarsana\IO\Interfaces\Parser;
use Tarsana\IO\Helper;

/**
 * Encodes and decodes YAML.
 */
class Yaml implements Parser, Dumper {
    /**
     * Decodes a YAML string and returns the resulting data.
     * 
     * @param  string $str
     * @param  array $config
     * @return mixed
     *
     * @throws Tarsana\IO\Exceptions\ParserException
     */
    public static function parse($str, $config = [])
    {
        try {
            return (new YamlParser())->parse($str, true);
        } catch (\Exception $e) {
            throw new ParserException('Error while parsing YAML');
        }
    }

    /**
     * Encodes data as a YAML string.
     * 
     * @param  mixed $data
     * @param  array $config
     * @return string
     *
     * @throws Tarsana\IO\Exceptions\DumperException
     */
    public static function dump($data, $config = [])
    {
        $defaultConfig = [
            'indent' => 2,
            'inline' => 0
        ];
        $config = array_merge($defaultConfig, $config);

        $yaml = new YamlDumper();
        $yaml->setIndentation($config['indent']);
        try {
           return $yaml->dump(Helper::toArray($data), 7, 0, true, true);
        } catch (\Exception $e) {
            throw new DumperException('Cannot encode data to YAML');
        }
    }
}
