<?php
require_once __DIR__ . '/utils.php';

use Tarsana\IO\Filesystem\Directory;
use Tarsana\IO\Filesystem\File;
use Tarsana\IO\Filesystem;


class FileTest extends PHPUnit_Framework_TestCase {

    protected $filePath;

    protected $file;

    public function setUp()
    {
        $this->filePath = path(__DIR__ . '/demo/temp.txt');
        $this->file = new File($this->filePath);
    }

    public function testCreatesFileIfMissing()
    {
        unlink($this->filePath);
        $this->assertFalse(is_file($this->filePath));
        
        $file = new File($this->filePath);
        $this->assertTrue(is_file($this->filePath));
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\FilesystemException
     */
    public function testThrowsExceptionWhenCannotCreateTheFile()
    {
        $file = new File(path(__DIR__ . '/demo/folder1'));
    }

    public function testGetsFilesystemInstance()
    {
        $this->assertTrue($this->file->fs() instanceof Filesystem);
    }

    public function testExists()
    {
        $this->assertTrue($this->file->exists());

        unlink($this->filePath);
        $this->assertFalse($this->file->exists());
    }

    public function testGetsAndSetsAbsolutePath()
    {
        $this->assertEquals($this->filePath, $this->file->path());
        
        $this->file->path(path(__DIR__ . '/demo/folder2/temp.txt'), true);
        $this->assertEquals(path(__DIR__ . '/demo/folder2/temp.txt'), $this->file->path());
        $this->assertTrue(is_file(path(__DIR__ . '/demo/folder2/temp.txt')));
        
        $this->file->path($this->filePath);
        $this->assertEquals($this->filePath, $this->file->path());
        $this->assertTrue(is_file($this->filePath));
    }

    public function testGetsAndSetsName()
    {
        $this->assertEquals('temp.txt', $this->file->name());

        $this->file->name('new-temp.txt');
        $this->assertEquals('new-temp.txt', $this->file->name());
        $this->assertEquals(path(__DIR__ . '/demo/new-temp.txt'), $this->file->path());
        $this->assertTrue(is_file(path(__DIR__ . '/demo/new-temp.txt')));
        
        $this->file->name('temp.txt');
        $this->assertEquals('temp.txt', $this->file->name());
        $this->assertEquals($this->filePath, $this->file->path());
        $this->assertTrue(is_file($this->filePath));
    }

    public function testGetsAndSetsPermissions()
    {
        chmod($this->filePath, 0755);
        $this->assertEquals('0755', $this->file->perms());

        $this->file->perms(0777);
        $this->assertEquals('0777', $this->file->perms());
    }

    public function testIsWritable()
    {
        chmod($this->filePath, 0444);
        $this->assertFalse($this->file->isWritable());
        
        chmod($this->filePath, 0666);
        $this->assertTrue($this->file->isWritable());
    }

    public function testIsExecutable()
    {
        chmod($this->filePath, 0444);
        $this->assertFalse($this->file->isExecutable());
        
        chmod($this->filePath, 0555);
        $this->assertTrue($this->file->isExecutable());
    }

    public function testGetsAndSetsExtension()
    {
        $this->assertEquals('txt', $this->file->extension());

        $this->file->name('temp.jpg');
        $this->assertEquals('jpg', $this->file->extension());

        $this->file->name('.gitignore');
        $this->assertEquals('gitignore', $this->file->extension());

        $this->file->name('without-extension');
        $this->assertEquals('', $this->file->extension());

        $this->file->name('temp.txt');
    }

    public function testGetsAndSetsContent()
    {
        file_put_contents($this->filePath, 'The obligatory Hello World !');
        $this->assertEquals('The obligatory Hello World !', $this->file->content());

        $this->file->content('A new content');
        $this->assertEquals('A new content', file_get_contents($this->filePath));
    }

    public function testAppend()
    {
        file_put_contents($this->filePath, 'The obligatory ');
        $this->file->append('Hello World !');

        $this->assertEquals('The obligatory Hello World !', file_get_contents($this->filePath));
    }

    public function testCopyAs()
    {
        file_put_contents($this->filePath, 'Some content');
        $copy = $this->file->copyAs(path(__DIR__ . '/demo/copies/new-temp.txt'));
        
        $this->assertTrue(is_file(path(__DIR__ . '/demo/copies/new-temp.txt')));
        $this->assertTrue($copy instanceof File);
        $this->assertEquals(path(__DIR__ . '/demo/copies/new-temp.txt'), $copy->path());
        $this->assertEquals('Some content', file_get_contents(path(__DIR__ . '/demo/copies/new-temp.txt')));

        (new Directory(path(__DIR__ . '/demo/copies')))->remove();
    }

    public function testGetsHash()
    {
        file_put_contents($this->filePath, 'The obligatory Hello World !');
        $this->assertEquals(md5_file($this->filePath), $this->file->hash());
    }

    public function testRemove()
    {
        $file = new File($this->filePath);
        $this->assertTrue(
            file_exists($this->filePath)
            && is_file($this->filePath)
        );

        $this->file->remove();
        $this->assertFalse(file_exists($this->filePath));
    }

    public function tearDown()
    {
        shell_exec('rm -rf ' . path(__DIR__.'/demo/temp*'));
    }

}
