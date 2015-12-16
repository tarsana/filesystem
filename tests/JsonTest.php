<?php

use Tarsana\IO\Json;


class JsonTest extends PHPUnit_Framework_TestCase {

    public function testParse()
    {
        $data = Json::parse('{"name":"foo","age":22,"valid":true,"friends":["bar","baz"]}');
        $object = (object) [
            'name'    => 'foo',
            'age'     =>  22,
            'valid'   =>  true,
            'friends' => [ 'bar', 'baz' ]
        ];
        $this->assertEquals($object, $data);

        $data = Json::parse('["one", "two", 3]');
        $this->assertEquals(["one", "two", 3], $data);
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\ParserException
     * @expectedExceptionMessage Error while parsing JSON
     */
    public function testParseInvalid()
    {
        Json::parse('{"name":"foo",}');
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
            '{"name":"foo","age":22,"valid":true,"friends":["bar","baz"]}',
            Json::dump($object)
        );

    }

}
