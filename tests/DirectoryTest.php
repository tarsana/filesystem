<?php
require_once __DIR__ . '/utils.php';

use Tarsana\IO\Filesystem;
use Tarsana\IO\Filesystem\Directory;


class DirectoryTest extends PHPUnit_Framework_TestCase {

    protected $dirPath;

    protected $dir;

    public function setUp()
    {
        $this->dirPath = path(__DIR__ . '/demo/folder1');
        $this->dir = new Directory($this->dirPath);
    }

    public function testCreatesDirectoryIfMissing()
    {
        $this->assertFalse(is_dir(path(__DIR__ .'/demo/temp')));
        
        $dir = new Directory(path(__DIR__ .'/demo/temp'));
        $this->assertTrue(is_dir(path(__DIR__ .'/demo/temp')));

        rmdir(path(__DIR__ .'/demo/temp'));
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\FilesystemException
     */
    public function testThrowsExceptionWhenCannotCreateTheDirectory()
    {
        $dir = new Directory(path(__DIR__ .'/demo/files.txt'));
    }

    public function testGetsFilesystemInstance()
    {
        $this->assertTrue($this->dir->fs() instanceof Filesystem);
    }

    public function testExists()
    {
        $this->assertTrue($this->dir->exists());

        $otherDir = new Directory(path(__DIR__ .'/demo/temp'));
        $this->assertTrue($otherDir->exists());

        rmdir(path(__DIR__ .'/demo/temp'));
        $this->assertFalse($otherDir->exists());
    }

    public function testGetsAndSetsAbsolutePath()
    {
        $this->assertEquals($this->dirPath, $this->dir->path());

        $this->dir->path(path(__DIR__ .'/demo/folder2/folder1'), true);
        $this->assertEquals(path(__DIR__ .'/demo/folder2/folder1'), $this->dir->path());
        $this->assertTrue(is_dir(path(__DIR__ .'/demo/folder2/folder1')));
        
        $this->dir->path($this->dirPath);
        $this->assertEquals($this->dirPath, $this->dir->path());
        $this->assertTrue(is_dir($this->dirPath));
    }

    public function testGetsAndSetsName()
    {
        $this->assertEquals('folder1', $this->dir->name());

        $this->dir->name('new-folder');
        $this->assertEquals('new-folder', $this->dir->name());
        $this->assertEquals(path(__DIR__ .'/demo/new-folder'), $this->dir->path());
        $this->assertTrue(is_dir(path(__DIR__ .'/demo/new-folder')));
        
        $this->dir->name('folder1');
        $this->assertEquals('folder1', $this->dir->name());
        $this->assertEquals($this->dirPath, $this->dir->path());
        $this->assertTrue(is_dir($this->dirPath));
    }

    public function testGetsAndSetsPermissions()
    {
        chmod($this->dirPath, 0744);
        clearstatcache(true, $this->dirPath);
        $this->assertEquals('0744', $this->dir->perms());
        
        $this->dir->perms(0777);
        $this->assertEquals('0777', $this->dir->perms());
    }

    public function testCopyAs()
    {
        $copy = $this->dir->copyAs(path(__DIR__ .'/demo/copies/folder1'));
        
        $this->assertTrue(is_dir(path(__DIR__ .'/demo/copies/folder1')));
        $this->assertTrue($copy instanceof Directory);
        $this->assertEquals(path(__DIR__ .'/demo/copies/folder1/'), $copy->path());

        $this->assertEquals(1, $copy->fs()->dirs()->count());
        $this->assertEquals(2, $copy->fs()->files()->count());

        $copy->remove();
    }

    public function testRemove()
    {
        $dir = new Directory(path(__DIR__ .'/demo/temp'));
        $this->assertTrue(
            file_exists(path(__DIR__ .'/demo/temp'))
            && is_dir(path(__DIR__ .'/demo/temp'))
        );

        $dir->remove();
        $this->assertFalse(file_exists(path(__DIR__ .'/demo/temp')));
    }

    public function tearDown()
    {
        shell_exec('rm -rf ' . path(__DIR__.'/demo/temp'));
        shell_exec('rm -rf ' . path(__DIR__.'/demo/copies'));
    }
}
