<?php
use Tarsana\IO\Filesystem;
use Tarsana\IO\Filesystem\Adapters\Local;
use Tarsana\IO\Filesystem\Collection;
use Tarsana\IO\Filesystem\Directory;
use Tarsana\IO\Filesystem\File;

/**
 * This uses the directory tests/demo as testing filesystem.
 * The tree of this directory is the following:
 *
 * folder1/
 *     folder11/
 *         some-doc.pdf
 *     some-doc.txt
 *     track.mp3
 * folder2/
 *     track1.mp3
 * folder3/
 * folder4/
 *     folder41/
 *         other.pdf
 *     folder42/
 *         other.mp3
 *     folder43/
 *         picture.jpg
 * files.txt
 *
 */
class LocalTest extends PHPUnit\Framework\TestCase {

    protected $fs;

    public function setUp()
    {
        $this->fs = new Filesystem(DEMO_DIR);
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\FilesystemException
     */
    public function test_throws_exception_if_root_directory_not_found()
    {
        $fs = new Filesystem(path(DEMO_DIR.'/none-present-folder'));
    }

    public function test_gets_root_path()
    {
        $this->assertEquals(path(DEMO_DIR.'/'), $this->fs->path());
    }

    public function test_gets_the_type_of_path_or_pattern()
    {
        $this->assertEquals('file', $this->fs->whatIs(path('folder1/some-doc.txt')));
        $this->assertEquals('file', $this->fs->whatIs(path('folder1/*-doc.txt')));
        $this->assertEquals('dir', $this->fs->whatIs('folder1'));
        $this->assertEquals('dir', $this->fs->whatIs('*1'));
        $this->assertEquals('nothing', $this->fs->whatIs(path('folder1/missing-doc.txt')));
        $this->assertEquals('nothing', $this->fs->whatIs(path('folder1/*.jpg')));
        $this->assertEquals('collection', $this->fs->whatIs(path('folder*/*.mp3')));
    }

    public function test_checks_if_file_exists()
    {
        $this->assertTrue($this->fs->isFile(path('folder1/track.mp3')));
        $this->assertFalse($this->fs->isFile(path('folder1/track.txt')));
    }

    public function test_checks_if_directory_exists()
    {
        $this->assertTrue($this->fs->isDir(path('folder4/folder42')));
        $this->assertFalse($this->fs->isDir('folder5'));
    }

    public function test_checks_if_file_or_directory_exists()
    {
        $this->assertTrue($this->fs->isAny(path('folder1/track.mp3')));
        $this->assertFalse($this->fs->isAny(path('folder1/track.txt')));
        $this->assertTrue($this->fs->isAny(path('folder4/folder42')));
        $this->assertFalse($this->fs->isAny('folder5'));
    }

    public function test_checks_if_multiple_files_exist()
    {
        $this->assertTrue($this->fs->areFiles([
            path('folder1/track.mp3'),
            'files.txt',
            path('folder4/folder41/other.pdf')
        ]));
        $this->assertFalse($this->fs->areFiles([
            'folder1',
            'files.txt'
        ]));
        $this->assertFalse($this->fs->areFiles([
            'some-missing-file.txt',
            'files.txt'
        ]));
    }

    public function test_checks_if_multiple_directories_exist()
    {
        $this->assertTrue($this->fs->areDirs([
            'folder1',
            path('folder4/folder41')
        ]));
        $this->assertFalse($this->fs->areDirs([
            'folder1',
            'files.txt'
        ]));
        $this->assertFalse($this->fs->areDirs([
            'folder1',
            'folder5'
        ]));
    }

    public function test_checks_if_multiple_files_or_directories_exist()
    {
        $this->assertTrue($this->fs->areAny([
            'folder1',
            path('folder4/folder41')
        ]));
        $this->assertTrue($this->fs->areAny([
            'folder1',
            'files.txt'
        ]));
        $this->assertFalse($this->fs->areAny([
            'folder1',
            'folder5'
        ]));
    }

    public function test_gets_or_creates_files_by_name()
    {
        $file = $this->fs->file('files.txt');
        $this->assertTrue($file instanceof File);
        $this->assertEquals('files.txt', $file->name());

        $file = $this->fs->file(path('tmp/file-to-be-created.txt'), true);
        $this->assertTrue($file instanceof File);
        $this->assertEquals('file-to-be-created.txt', $file->name());

        $files = $this->fs->files([path('folder4/folder43/picture.jpg'), 'files.txt']);
        $this->assertTrue($files instanceof Collection);
        $this->assertEquals(2, $files->count());

        $files = $this->fs->files(); // all files under root directory
        $this->assertTrue($files instanceof Collection);
        $this->assertEquals(1, $files->count());

        $this->fs->dir('tmp')->remove();
    }

    public function test_gets_or_creates_directories_by_name()
    {
        $dir = $this->fs->dir('folder1');
        $this->assertTrue($dir instanceof Directory);
        $this->assertEquals('folder1', $dir->name());

        $dir = $this->fs->dir(path('tmp/folder-to-be-created/sub-folder'), true);
        $this->assertTrue($dir instanceof Directory);
        $this->assertEquals('sub-folder', $dir->name());
        $this->tearDown(); // removes the tmp folder

        $dirs = $this->fs->dirs([path('folder4/folder43'), path('folder2')]);
        $this->assertTrue($dirs instanceof Collection);
        $this->assertEquals(2, $dirs->count());

        $dirs = $this->fs->dirs(); // all directories under root directory
        $this->assertTrue($dirs instanceof Collection);
        $this->assertEquals(4, $dirs->count());
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\FilesystemException
     */
    public function test_throws_exception_if_file_not_found()
    {
        $this->fs->file('none-present-file.txt');
    }

    /**
     * @expectedException Tarsana\IO\Exceptions\FilesystemException
     */
    public function test_throws_exception_if_directory_not_found()
    {
        $this->fs->dir('none-present-folder');
    }

    public function test_gets_files_or_directories_matching_pattern()
    {
        $found = $this->fs->find('f*');
        $this->assertTrue($found instanceof Collection);
        $this->assertEquals(5, $found->count());
        $this->assertEquals(1, $found->files()->count());
        $this->assertEquals(4, $found->dirs()->count());
    }

    public function test_removes_files_and_directories()
    {
        $file = $this->fs->file(path('tmp/file.php'), true);
        $this->assertTrue($this->fs->isFile(path('tmp/file.php')));
        $this->fs->remove(path('tmp/file.php'));
        $this->assertFalse($this->fs->isFile(path('tmp/file.php')));

        $dir = $this->fs->dir(path('tmp/dir'), true);
        $this->assertTrue($this->fs->isDir(path('tmp/dir')));
        $this->fs->remove(path('tmp/dir'));
        $this->assertFalse($this->fs->isDir(path('tmp/file.php')));

        $file = $this->fs->file(path('tmp/file.php'), true);
        $dir = $this->fs->dir(path('tmp/dir'), true);
        $this->fs->removeAll([path('tmp/file.php', 'tmp/dir')]);
        $this->assertFalse($this->fs->isFile(path('tmp/file.php')));
        $this->assertFalse($this->fs->isDir(path('tmp/file.php')));
    }

    public function tearDown()
    {
        remove(path(DEMO_DIR.'/tmp'));
    }
}
