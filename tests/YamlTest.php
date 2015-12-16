<?php

use Tarsana\IO\Yaml;


class YamlTest extends PHPUnit_Framework_TestCase {

    public function testParse()
    {
        $data = Yaml::parse(
            'name: foo' . PHP_EOL .
            'age: 22' . PHP_EOL .
            'valid: true' . PHP_EOL .
            'friends:' . PHP_EOL .
            '  - bar' . PHP_EOL .
            '  - baz' . PHP_EOL
        );
        $object = [
            'name'    => 'foo',
            'age'     =>  22,
            'valid'   =>  true,
            'friends' => [ 'bar', 'baz' ]
        ];
        $this->assertEquals($object, $data);

        $data = Yaml::parse(
            '- one' . PHP_EOL .
            '- two' . PHP_EOL .
            '- 3'
        );
        $this->assertEquals(["one", "two", 3], $data);
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\ParserException
     * @expectedExceptionMessage Error while parsing YAML
     */
    public function testParseInvalid()
    {
        Yaml::parse(
            '---' . PHP_EOL .
            '  - foo' . PHP_EOL .
            ' bar'
        );
    }

    public function testDump()
    {
        $object = (object) [
            'name'    => 'foo',
            'age'     =>  22,
            'valid'   =>  true,
            'friends' => [ 'bar', 'baz' ]
        ];

        $this->assertEquals(
            'name: foo' . PHP_EOL .
            'age: 22' . PHP_EOL .
            'valid: true' . PHP_EOL .
            'friends:' . PHP_EOL .
            '  - bar' . PHP_EOL .
            '  - baz' . PHP_EOL,
            Yaml::dump($object)
        );

    }

}
