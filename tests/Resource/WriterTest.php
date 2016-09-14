<?php

use Tarsana\IO\Resource\Writer;

class WriterTest extends PHPUnit_Framework_TestCase {

    protected $writer;

    protected $path;

    public function setUp()
    {
        $this->path = path(DEMO_DIR.'/temp.txt');
        file_put_contents($this->path, "");
        $this->writer = new Writer($this->path);
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\ResourceHandlerException
     */
    public function test_fails_if_not_writable()
    {
        $writer = new Writer(fopen('php://memory', 'r'));
    }

    public function test_constructor()
    {
        new Writer;
    }

    public function test_close()
    {
        $resource = fopen('php://memory', 'w');
        $in = new Writer($resource);
        $this->assertTrue(is_resource($resource));
        $in->close();
        $this->assertFalse(is_resource($resource));
    }

    public function test_writes_content()
    {
        $this->writer->write("Hello");
        $this->assertEquals("Hello", file_get_contents($this->path));
    }

    public function tearDown()
    {
        remove(path(DEMO_DIR.'/temp.txt'));
    }

}
