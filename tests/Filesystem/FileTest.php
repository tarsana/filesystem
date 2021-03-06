<?php

use Tarsana\Filesystem\Filesystem;
use Tarsana\Filesystem\Adapters\Local;
use Tarsana\Filesystem\Directory;
use Tarsana\Filesystem\File;


class FileTest extends PHPUnit\Framework\TestCase {

    protected $filePath;

    protected $file;

    public function setUp()
    {
        $this->filePath = DEMO_DIR.'/temp.txt';
        $this->file = new File($this->filePath);
    }

    public function test_creates_file_if_missing()
    {
        unlink($this->filePath);
        $this->assertFalse(is_file($this->filePath));

        $file = new File($this->filePath);
        $this->assertTrue(is_file($this->filePath));
    }

    /**
     * @expectedException Tarsana\Filesystem\Exceptions\FilesystemException
     */
    public function test_throws_exception_when_directory_exists_with_same_path()
    {
        $file = new File(DEMO_DIR.'/folder1');
    }

    public function test_gets_filesystem_instance()
    {
        $this->assertTrue($this->file->fs() instanceof Filesystem);
    }

    public function test_exists()
    {
        $this->assertTrue($this->file->exists());

        unlink($this->filePath);
        $this->assertFalse($this->file->exists());
    }

    public function test_gets_and_sets_absolute_path()
    {
        $this->assertEquals($this->filePath, $this->file->path());

        $this->file->path(DEMO_DIR.'/folder2/temp.txt', true);
        $this->assertEquals(DEMO_DIR.'/folder2/temp.txt', $this->file->path());
        $this->assertTrue(is_file(DEMO_DIR.'/folder2/temp.txt'));

        $this->file->path($this->filePath);
        $this->assertEquals($this->filePath, $this->file->path());
        $this->assertTrue(is_file($this->filePath));
    }

    /**
     * @expectedException Tarsana\Filesystem\Exceptions\FilesystemException
     */
    public function test_throws_exception_when_already_exists_path()
    {
        $path = DEMO_DIR.'/temp-2.txt';
        file_put_contents($path, '');
        $this->file->path($path);
    }

    public function test_gets_and_sets_name()
    {
        $this->assertEquals('temp.txt', $this->file->name());

        $this->file->name('new-temp.txt');
        $this->assertEquals('new-temp.txt', $this->file->name());
        $this->assertEquals(DEMO_DIR.'/new-temp.txt', $this->file->path());
        $this->assertTrue(is_file(DEMO_DIR.'/new-temp.txt'));

        $this->file->name('temp.txt');
        $this->assertEquals('temp.txt', $this->file->name());
        $this->assertEquals($this->filePath, $this->file->path());
        $this->assertTrue(is_file($this->filePath));
    }

    /**
     * @expectedException Tarsana\Filesystem\Exceptions\FilesystemException
     */
    public function test_throws_exception_when_invalid_name()
    {
        $this->file->name('');
    }

    public function test_gets_and_sets_permissions()
    {
        chmod($this->filePath, 0755);
        $this->assertEquals('0755', $this->file->perms());

        $this->file->perms(0777);
        $this->assertEquals('0777', $this->file->perms());
    }

    public function test_is_writable()
    {
        chmod($this->filePath, 0444);
        $this->assertFalse($this->file->isWritable());

        chmod($this->filePath, 0666);
        $this->assertTrue($this->file->isWritable());
    }

    public function test_is_executable()
    {
        chmod($this->filePath, 0444);
        $this->assertFalse($this->file->isExecutable());

        chmod($this->filePath, 0555);
        $this->assertTrue($this->file->isExecutable());

        chmod($this->filePath, 0777);
    }

    public function test_sets_and_gets_extension()
    {
        $this->assertEquals('txt', $this->file->extension());

        $this->file->extension('jpg');
        $this->assertEquals('jpg', $this->file->extension());

        $this->file->name('.gitignore');
        $this->assertEquals('gitignore', $this->file->extension());

        $this->file->name('without-extension');
        $this->assertEquals('', $this->file->extension());

        $this->file->name('temp.txt');
    }

    public function test_gets_and_sets_content()
    {
        file_put_contents($this->filePath, 'The obligatory Hello World !');
        $this->assertEquals('The obligatory Hello World !', $this->file->content());

        $this->file->content('A new content');
        $this->assertEquals('A new content', file_get_contents($this->filePath));
    }

    public function test_append()
    {
        file_put_contents($this->filePath, 'The obligatory ');
        $this->file->append('Hello World !');

        $this->assertEquals('The obligatory Hello World !', file_get_contents($this->filePath));
    }

    public function test_copy_as()
    {
        file_put_contents($this->filePath, 'Some content');
        $copy = $this->file->copyAs(DEMO_DIR.'/copies/new-temp.txt');

        $this->assertTrue(is_file(DEMO_DIR.'/copies/new-temp.txt'));
        $this->assertTrue($copy instanceof File);
        $this->assertEquals(DEMO_DIR.'/copies/new-temp.txt', $copy->path());
        $this->assertEquals('Some content', file_get_contents(DEMO_DIR.'/copies/new-temp.txt'));

        (new Directory(DEMO_DIR.'/copies'))->remove();
    }

    public function test_gets_hash()
    {
        file_put_contents($this->filePath, 'The obligatory Hello World !');
        $this->assertEquals(md5_file($this->filePath), $this->file->hash());
    }

    public function test_remove()
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
        remove(DEMO_DIR.'/temp.txt');
        remove(DEMO_DIR.'/temp-2.txt');
    }

}
