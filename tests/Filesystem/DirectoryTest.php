<?php

use Tarsana\IO\Filesystem;
use Tarsana\IO\Filesystem\Adapters\Local;
use Tarsana\IO\Filesystem\Directory;


class DirectoryTest extends PHPUnit\Framework\TestCase {

    protected $dirPath;

    protected $dir;

    public function setUp()
    {
        $this->dirPath = path(DEMO_DIR.'/folder1');
        $this->dir = new Directory($this->dirPath);
    }

    public function test_creates_directory_if_missing()
    {
        $this->assertFalse(is_dir(path(DEMO_DIR.'/temp')));

        $dir = new Directory(path(DEMO_DIR.'/temp'));
        $this->assertTrue(is_dir(path(DEMO_DIR.'/temp')));

        rmdir(path(DEMO_DIR.'/temp'));
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\FilesystemException
     */
    public function test_throws_exception_when_cannot_create_the_directory()
    {
        $dir = new Directory(path(DEMO_DIR.'/files.txt'));
    }

    public function test_gets_filesystem_instance()
    {
        $this->assertTrue($this->dir->fs() instanceof Filesystem);
    }

    public function test_exists()
    {
        $this->assertTrue($this->dir->exists());

        $otherDir = new Directory(path(DEMO_DIR.'/temp'));
        $this->assertTrue($otherDir->exists());

        rmdir(path(DEMO_DIR.'/temp'));
        $this->assertFalse($otherDir->exists());
    }

    public function test_gets_and_sets_absolute_path()
    {
        $this->assertEquals($this->dirPath, $this->dir->path());

        $this->dir->path(path(DEMO_DIR.'/folder2/folder1'), true);
        $this->assertEquals(path(DEMO_DIR.'/folder2/folder1'), $this->dir->path());
        $this->assertTrue(is_dir(path(DEMO_DIR.'/folder2/folder1')));

        $this->dir->path($this->dirPath);
        $this->assertEquals($this->dirPath, $this->dir->path());
        $this->assertTrue(is_dir($this->dirPath));
    }

    public function test_gets_and_sets_name()
    {
        $this->assertEquals('folder1', $this->dir->name());

        $this->dir->name('new-folder');
        $this->assertEquals('new-folder', $this->dir->name());
        $this->assertEquals(path(DEMO_DIR.'/new-folder'), $this->dir->path());
        $this->assertTrue(is_dir(path(DEMO_DIR.'/new-folder')));

        $this->dir->name('folder1');
        $this->assertEquals('folder1', $this->dir->name());
        $this->assertEquals($this->dirPath, $this->dir->path());
        $this->assertTrue(is_dir($this->dirPath));
    }

    // public function test_gets_and_sets_permissions()
    // {
    //     chmod($this->dirPath, 0744);
    //     clearstatcache(true, $this->dirPath);
    //     $this->assertEquals('0744', $this->dir->perms());

    //     $this->dir->perms(0777);
    //     $this->assertEquals('0777', $this->dir->perms());
    // }

    public function test_copy_as()
    {
        $copy = $this->dir->copyAs(path(DEMO_DIR.'/copies/folder1'));

        $this->assertTrue(is_dir(path(DEMO_DIR.'/copies/folder1')));
        $this->assertTrue($copy instanceof Directory);
        $this->assertEquals(path(DEMO_DIR.'/copies/folder1'), $copy->path());

        $this->assertEquals(1, $copy->fs()->dirs()->count());
        $this->assertEquals(2, $copy->fs()->files()->count());

        $copy->remove();
    }

    public function test_remove()
    {
        $dir = new Directory(path(DEMO_DIR.'/temp'));
        $this->assertTrue(
            file_exists(path(DEMO_DIR.'/temp'))
            && is_dir(path(DEMO_DIR.'/temp'))
        );

        $dir->remove();
        $this->assertFalse(file_exists(path(DEMO_DIR.'/temp')));
    }

    public function tearDown()
    {
        remove(path(DEMO_DIR.'/temp'));
        remove(path(DEMO_DIR.'/copies'));
    }
}
