<?php
require_once __DIR__ . '/utils.php';

use Tarsana\IO\Interfaces\Writer;
use Tarsana\IO\Resources\InputResource;

class InputResourceTest extends PHPUnit_Framework_TestCase {

    protected $ir;

    public function setUp()
    {
        $path = path(__DIR__ . '/demo/temp.txt');
        file_put_contents($path, "Hello World !".PHP_EOL."Second line".PHP_EOL."final line");
        $this->ir = new InputResource($path);
    }

    public function testReadsContent()
    {
        $this->assertEquals(
            "Hello World !".PHP_EOL."Second line".PHP_EOL."final line",
            $this->ir->read()
        );
    }

    public function testReadsLineByLine()
    {
        $this->assertEquals(
            "Hello World !".PHP_EOL,
            $this->ir->readLine()
        );
        $this->assertEquals(
            "Second line".PHP_EOL,
            $this->ir->readLine()
        );
        $this->assertEquals(
            "final line",
            $this->ir->readLine()
        );
    }

    public function testNonBlocking()
    {
        $in = new InputResource;
        $in->blocking(false);
        $this->assertEquals("", $in->read());
    }

    public function testPipesContent()
    {
        $out = new WriterMock;
        $this->ir->pipe($out);
        $this->assertEquals(
            "Hello World !".PHP_EOL."Second line".PHP_EOL."final line",
            $out->content
        );
    }

    public function testClose()
    {
        $resource = fopen('php://memory', 'r');
        $in = new InputResource($resource);
        $this->assertTrue(is_resource($resource));
        $in->close();
        $this->assertFalse(is_resource($resource));
    }

    public function tearDown()
    {
        shell_exec('rm -rf ' . path(__DIR__.'/demo/temp*'));
    }

}
