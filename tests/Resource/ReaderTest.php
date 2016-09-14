<?php

use Tarsana\IO\Resource\Reader;

class ReaderTest extends PHPUnit_Framework_TestCase {

    protected $reader;

    public function setUp()
    {
        $path = path(DEMO_DIR.'/temp.txt');
        file_put_contents($path, "Hello World !");
        $this->reader = new Reader($path);
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\ResourceHandlerException
     */
    public function test_fails_if_not_readable()
    {
        $writer = new Reader(fopen(path(DEMO_DIR.'/temp.txt'), 'w'));
    }

    public function test_reads_hole_content()
    {
        $this->assertEquals(
            "Hello World !",
            $this->reader->read()
        );
    }

    public function test_reads_part_of_content()
    {
        $this->assertEquals(
            "Hello",
            $this->reader->read(5)
        );

        $this->assertEquals(
            " World",
            $this->reader->read(6)
        );

        $this->assertEquals(
            " !",
            $this->reader->read()
        );
    }

    public function test_non_blocking()
    {
        $in = new Reader;
        $in->blocking(false);
        $this->assertEquals("", $in->read());
    }

    public function test_close()
    {
        $resource = fopen('php://memory', 'r');
        $in = new Reader($resource);
        $this->assertTrue(is_resource($resource));
        $in->close();
        $this->assertFalse(is_resource($resource));
    }

    public function tearDown()
    {
        remove(path(DEMO_DIR.'/temp.txt'));
    }
}
