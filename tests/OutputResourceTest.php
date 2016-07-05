<?php
require_once __DIR__ . '/utils.php';

use Tarsana\IO\Resources\OutputResource;

class OutputResourceTest extends PHPUnit_Framework_TestCase {

    protected $or;

    protected $path;

    public function setUp()
    {
        $this->path = path(__DIR__ . '/demo/temp.txt');
        file_put_contents($this->path, "");
        $this->or = new OutputResource($this->path);
    }

    public function testClose()
    {
        $resource = fopen('php://memory', 'r');
        $in = new OutputResource($resource);
        $this->assertTrue(is_resource($resource));
        $in->close();
        $this->assertFalse(is_resource($resource));
    }

    public function testWritesContent()
    {
        $this->or->write("Hello");
        $this->assertEquals("Hello", file_get_contents($this->path));

        $this->or->writeLine(" World !");
        $this->assertEquals("Hello World !".PHP_EOL, file_get_contents($this->path));
    }

    public function tearDown()
    {
        shell_exec('rm -rf ' . path(__DIR__.'/demo/temp*'));
    }

}
