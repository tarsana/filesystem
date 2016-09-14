<?php

use Tarsana\IO\Resource\Buffer;

class BufferTest extends PHPUnit_Framework_TestCase {

    protected $buffer;

    protected $path;

    public function setUp()
    {
        $this->path = path(DEMO_DIR.'/temp.txt');
        file_put_contents($this->path, "Hello World !");
        $this->buffer = new Buffer($this->path);
    }

    public function test_reads_content()
    {
        $this->assertEquals(
            "Hello World !",
            $this->buffer->read()
        );
    }

    public function test_non_blocking()
    {
        $in = new Buffer;
        $in->blocking(false);
        $this->assertEquals("", $in->read());
    }

    public function test_close()
    {
        $resource = fopen('php://memory', 'r+');
        $in = new Buffer($resource);
        $this->assertTrue(is_resource($resource));
        $in->close();
        $this->assertFalse(is_resource($resource));
    }

    public function test_writes_content()
    {
        $this->buffer->write(" Hello");
        $this->assertEquals("Hello World ! Hello", file_get_contents($this->path));
    }

    public function test_reads_and_writes_content()
    {
        $this->assertEquals("Hello ", $this->buffer->read(6));
        $this->buffer->write(' Yo');
        $this->assertEquals("World ", $this->buffer->read(6));
        $this->assertEquals("! Yo", $this->buffer->read());
    }

    public function tearDown()
    {
        remove(path(DEMO_DIR.'/temp.txt'));
    }

}
