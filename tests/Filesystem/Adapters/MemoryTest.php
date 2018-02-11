<?php
use Tarsana\Filesystem\Filesystem;
use Tarsana\Filesystem\Adapters\Memory;

class MemoryTest extends PHPUnit\Framework\TestCase {

    protected $m;

    public function setUp()
    {
        /**
         * foo/
         *   bar/
         *     baz/
         *     aww.jpg
         *   todo.txt
         *   track.mp3
         * lorem/
         *   ipsum/
         */
        $m = new Memory;
        $m->mkdir('foo/bar/baz', 0755, true);
        $m->mkdir('lorem/ipsum', 0755, true);
        $m->filePutContents('foo/todo.txt', 'Write Awesome Code');
        $m->createFile('foo/track.mp3');
        $m->createFile('foo/bar/aww.jpg');
        $this->m = $m;
    }

    public function test_realpath()
    {
        $this->assertEquals(getcwd() . '/foo', $this->m->realpath('./foo'));
        $this->assertEquals(getcwd() . '/foo', $this->m->realpath('./foo/bar/../.'));
        $this->assertEquals(getcwd() . '/foo', $this->m->realpath('foo/./bar/..'));
    }

    public function test_glob()
    {

        $this->assertEquals([], $this->m->glob('aa/*'));
        $this->assertEquals([getcwd() . '/foo/bar/aww.jpg'], $this->m->glob('foo/*/*.jpg'));
        $this->assertEquals([getcwd() . '/foo', getcwd() . '/lorem'], $this->m->glob('*'));
    }

    public function test_isFile()
    {
        $this->assertTrue($this->m->isFile('foo/todo.txt'));
        $this->assertFalse($this->m->isFile('foo/bar'));
        $this->assertFalse($this->m->isFile('foo/missing'));
    }

    public function test_isDir()
    {
        $this->assertTrue($this->m->isDir('foo/bar'));
        $this->assertFalse($this->m->isDir('foo/todo.txt'));
        $this->assertFalse($this->m->isDir('foo/missing'));
    }

    public function test_fileExists()
    {
        $this->assertTrue($this->m->fileExists('foo/bar'));
        $this->assertTrue($this->m->fileExists('foo/todo.txt'));
        $this->assertFalse($this->m->fileExists('foo/missing'));
    }

    public function test_md5File()
    {
        $this->assertEquals(md5('Write Awesome Code'), $this->m->md5File('foo/todo.txt'));
        $this->assertFalse($this->m->md5File('foo/missing.txt'));
    }

    public function test_fileGetContents()
    {
        $this->assertEquals('Write Awesome Code', $this->m->fileGetContents('foo/todo.txt'));
        $this->assertFalse($this->m->fileGetContents('foo/missing.txt'));
    }

    public function test_filePutContents()
    {
        $this->m->filePutContents('foo/todo.txt', 'Get Enough Sleep');
        $this->m->filePutContents('foo/missing.txt', 'Hello');
        $this->assertEquals('Get Enough Sleep', $this->m->fileGetContents('foo/todo.txt'));
        $this->assertEquals('Hello', $this->m->fileGetContents('foo/missing.txt'));
        $this->m->filePutContents('foo/missing.txt', ' World', FILE_APPEND);
        $this->assertEquals('Hello World', $this->m->fileGetContents('foo/missing.txt'));
    }

    public function test_isReadable()
    {
        $this->m->chmod('foo/todo.txt', 0220);
        $this->m->chmod('foo/bar', 0220);
        $this->assertFalse($this->m->isReadable('foo/todo.txt'));
        $this->assertFalse($this->m->isReadable('foo/bar'));
        $this->m->chmod('foo/todo.txt', 0664);
        $this->m->chmod('foo/bar', 0775);
        $this->assertTrue($this->m->isReadable('foo/todo.txt'));
        $this->assertTrue($this->m->isReadable('foo/bar'));
    }

    public function test_isWritable()
    {
        $this->m->chmod('foo/todo.txt', 0555);
        $this->m->chmod('foo/bar', 0555);
        $this->assertFalse($this->m->isWritable('foo/todo.txt'));
        $this->assertFalse($this->m->isWritable('foo/bar'));
        $this->m->chmod('foo/todo.txt', 0666);
        $this->m->chmod('foo/bar', 0777);
        $this->assertTrue($this->m->isWritable('foo/todo.txt'));
        $this->assertTrue($this->m->isWritable('foo/bar'));
    }

    public function test_isExecutable()
    {
        $this->m->chmod('foo/todo.txt', 0666);
        $this->m->chmod('foo/bar', 0666);
        $this->assertFalse($this->m->isExecutable('foo/todo.txt'));
        $this->assertFalse($this->m->isExecutable('foo/bar'));
        $this->m->chmod('foo/todo.txt', 0777);
        $this->m->chmod('foo/bar', 0777);
        $this->assertTrue($this->m->isExecutable('foo/todo.txt'));
        $this->assertTrue($this->m->isExecutable('foo/bar'));
    }

    public function test_unlink()
    {
        $this->assertTrue($this->m->fileExists('foo/todo.txt'));
        $this->m->unlink('foo/todo.txt');
        $this->assertFalse($this->m->fileExists('foo/todo.txt'));
    }

    public function test_rmdir()
    {
        $this->assertTrue($this->m->fileExists('foo/bar/baz'));
        $this->assertTrue($this->m->rmdir('foo/bar/baz'));
        $this->assertFalse($this->m->fileExists('foo/bar/baz'));

        $this->assertFalse($this->m->rmdir('foo/bar'));
        $this->assertTrue($this->m->fileExists('foo/bar'));
    }

    public function test_rename()
    {
        $this->assertTrue($this->m->rename('foo/todo.txt', 'foo/tasks.txt'));
        $this->assertFalse($this->m->fileExists('foo/todo.txt'));
        $this->assertTrue($this->m->fileExists('foo/tasks.txt'));
        $this->assertEquals('Write Awesome Code', $this->m->fileGetContents('foo/tasks.txt'));

        $this->assertFalse($this->m->rename('foo/missing.txt', 'foo/something.txt'));

        $this->assertTrue($this->m->rename('foo/bar', 'lorem/new'));
        $this->assertFalse($this->m->fileExists('foo/bar'));
        $this->assertFalse($this->m->fileExists('foo/bar/baz'));
        $this->assertFalse($this->m->fileExists('foo/bar/aw.jpg'));
        $this->assertTrue($this->m->fileExists('lorem/new'));
        $this->assertTrue($this->m->fileExists('lorem/new/baz'));
        $this->assertTrue($this->m->fileExists('lorem/new/aww.jpg'));
    }

    public function test_fileperms_chmod()
    {
        $this->m->chmod('foo/todo.txt', 0666);
        $this->assertEquals(0666, $this->m->fileperms('foo/todo.txt'));
        $this->m->chmod('foo/todo.txt', 0767);
        $this->assertEquals(0767, $this->m->fileperms('foo/todo.txt'));
        $this->assertFalse($this->m->chmod('foo/missing.txt', 664));
        $this->assertFalse($this->m->fileperms('foo/missing.txt'));
    }

    public function test_extension()
    {
        $this->assertEquals('jpg', $this->m->extension('foo/bar/aww.jpg'));
        $this->assertEquals('txt', $this->m->extension('foo/todo.txt'));
        $this->assertEquals('', $this->m->extension('foo/missing'));
        $this->assertEquals('', $this->m->extension('foo/bar'));
    }

    public function test_basename()
    {
        $this->assertEquals('aww.jpg', $this->m->basename('foo/bar/aww.jpg'));
        $this->assertEquals('todo.txt', $this->m->basename('foo/todo.txt'));
        $this->assertEquals('missing', $this->m->basename('foo/missing'));
        $this->assertEquals('bar', $this->m->basename('foo/bar'));
    }
}
