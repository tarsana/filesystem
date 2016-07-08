<?php
require_once __DIR__ . '/utils.php';

use Tarsana\Functional as F;
use Tarsana\IO\Resources\PipeResource;

class PipeResourceTest extends PHPUnit_Framework_TestCase {

    protected $pr;

    protected $path;

    public function setUp()
    {
        $this->path = path(__DIR__ . '/demo/temp.txt');
        file_put_contents($this->path, "Hello World !".PHP_EOL."Second line".PHP_EOL."final line");
        $this->pr = new PipeResource($this->path);
    }

    public function testReadsContent()
    {
        $this->assertEquals(
            "Hello World !".PHP_EOL."Second line".PHP_EOL."final line",
            $this->pr->read()
        );
    }

    public function testReadsLineByLine()
    {
        $this->assertEquals(
            "Hello World !".PHP_EOL,
            $this->pr->readLine()
        );
        $this->assertEquals(
            "Second line".PHP_EOL,
            $this->pr->readLine()
        );
        $this->assertEquals(
            "final line",
            $this->pr->readLine()
        );
    }

    public function testNonBlocking()
    {
        $in = new PipeResource;
        $in->blocking(false);
        $this->assertEquals("", $in->read());
    }

    public function testPipesContent()
    {
        $out = new WriterMock;
        $this->pr->pipe($out);
        $this->assertEquals(
            "Hello World !".PHP_EOL."Second line".PHP_EOL."final line",
            $out->content
        );
    }

    public function testStream()
    {
        $s = $this->pr
            ->stream()
            ->then(F\split(PHP_EOL));

        $this->assertEquals(["Hello World !", "Second line", "final line"], $s->get());
    }

    public function testClose()
    {
        $resource = fopen('php://memory', 'r');
        $in = new PipeResource($resource);
        $this->assertTrue(is_resource($resource));
        $in->close();
        $this->assertFalse(is_resource($resource));
    }

    public function testWritesContent()
    {
        $this->pr->write(" Hello");
        $this->assertEquals("Hello World !".PHP_EOL."Second line".PHP_EOL."final line Hello", file_get_contents($this->path));

        $this->pr->writeLine(" World");
        $this->assertEquals("Hello World !".PHP_EOL."Second line".PHP_EOL."final line Hello World".PHP_EOL, file_get_contents($this->path));
    }

    public function tearDown()
    {
        shell_exec('rm -rf ' . path(__DIR__.'/demo/temp*'));
    }

}
