<?php

use Tarsana\Filesystem\Adapters\Local;
use Tarsana\Filesystem\Resource\Buffer;

class BufferTest extends PHPUnit\Framework\TestCase {

    protected $buffer;

    protected $path;

    public function setUp()
    {
        $this->path = DEMO_DIR.'/temp.txt';
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

        $this->buffer->writeLine('First line');
        $this->buffer->writeLine('Second line');
        $this->assertEquals(
            "First line" . PHP_EOL . "Second line" . PHP_EOL,
            $this->buffer->read()
        );

        $this->buffer->writeLine('First line');
        $this->buffer->writeLine('Second line');
        $this->assertEquals(
            "First line",
            $this->buffer->readLine()
        );

        $this->assertEquals(
            "Second ",
            $this->buffer->readUntil('line')
        );
    }

    /**
     * @expectedException Tarsana\Filesystem\Exceptions\ResourceException
     */
    public function test_throws_exception_if_empty_ending_word_given()
    {
        $this->buffer->readUntil('');
    }

    public function tearDown()
    {
        remove(DEMO_DIR.'/temp.txt');
    }

}
