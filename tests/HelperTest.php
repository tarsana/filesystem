<?php

use Tarsana\IO\Helper;


class HelperTest extends PHPUnit_Framework_TestCase {

    public function testConvertToArray()
    {
        // string
        $this->assertEquals(
            ['string value'], 
            Helper::toArray('string value')
        );
        // number
        $this->assertEquals(
            [114], 
            Helper::toArray(114)
        );
        // boolean
        $this->assertEquals(
            [true], 
            Helper::toArray(true)
        );
        // array
        $this->assertEquals(
            ['one', 'two', 3], 
            Helper::toArray(['one', 'two', 3])
        );
        $this->assertEquals(
            ["one" => 1, "two" => 2], 
            Helper::toArray(['one' => 1, 'two' => 2])
        );
        // function or Closure
        $this->assertEquals(
            null, 
            Helper::toArray(function(){ return ':P'; })
        );
        // object
        $object = new \stdClass;
        $object->str = 'string value';
        $object->arr = ['one', 'two', 'three'];
        $object->fn  = function($arg){ return 'result'; };
        $object->obj = new Foo('Foo', 'Instance');

        $array = [
            'str' => 'string value',
            'arr' => ['one', 'two', 'three'],
            'obj' => [
                'name' => 'Foo',
                'job' => 'Instance'
            ]
        ];

        $this->assertEquals($array, Helper::toArray($object));
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\HelperException
     * @expectedExceptionMessage Error while converting data to array
     */
    public function testConvertInvalidDataToArray()
    {
        $a = new \stdClass;
        $b = new \stdClass;

        $a->name = 'A';
        $a->next = $b;

        $b->name = 'B';
        $b->next = $a;

        Helper::toArray($a);
    }

}

class Foo {
    public $name;
    protected $job;
    protected $speak;
    public function __construct($name, $job)
    {
        $this->name = $name;
        $this->job = $job;

        $me = $this;
        $this->speak = function(){
            return "Silence is golden.";
        };
    }
    public function sayHi()
    {
        echo "Yo! Let's code something awesome !";
    }
}
