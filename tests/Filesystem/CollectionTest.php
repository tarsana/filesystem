<?php

use Tarsana\IO\Filesystem\Adapters\Local;
use Tarsana\IO\Filesystem\Collection;
use Tarsana\IO\Filesystem\Directory;
use Tarsana\IO\Filesystem\File;


class CollectionTest extends PHPUnit\Framework\TestCase {

    protected $collection;

    protected $tempPath;

    public function setUp()
    {
        $this->tempPath = path(DEMO_DIR.'/temp/');
        $this->collection = new Collection([
            new File(path($this->tempPath.'file1.txt')),
            new File(path($this->tempPath.'file2.txt')),
            new Directory(path($this->tempPath.'dir1')),
            new Directory(path($this->tempPath.'dir2')),
            new Directory(path($this->tempPath.'dir1/dir11')),
            new File(path($this->tempPath.'dir1/file11.txt'))
        ]);
    }

    public function test_adding_and_counting_elements()
    {
        $this->assertEquals(6, $this->collection->count());

        $this->collection->add(new File(path($this->tempPath.'file3.txt')));
        $this->assertEquals(7, $this->collection->count());

        // Does not add the same file twice
        $this->collection->add(new File(path($this->tempPath.'file1.txt')));
        $this->assertEquals(7, $this->collection->count());
    }

    public function test_contains()
    {
        $this->assertTrue($this->collection->contains(path($this->tempPath.'file1.txt')));
        $this->assertFalse($this->collection->contains(path($this->tempPath.'file.txt')));
    }

    public function test_gets_file_or_directory_by_path()
    {
        $file = $this->collection->get(path($this->tempPath.'file1.txt'));
        $this->assertTrue($file instanceof File);
        $this->assertEquals('file1.txt', $file->name());
    }

    public function test_updates_paths_automatically()
    {
        $file = $this->collection->get(path($this->tempPath.'file1.txt'));
        $this->assertFalse($this->collection->contains(path($this->tempPath.'other.txt')));
        $file->name('other.txt', true);
        $this->assertTrue($this->collection->contains(path($this->tempPath.'other.txt')));
    }

    public function test_gets_all()
    {
        $array = $this->collection->asArray();

        $this->assertTrue(is_array($array));
        $this->assertEquals(6, count($array));
    }

    public function test_gets_files()
    {
        $files = $this->collection->files();

        $this->assertTrue($files instanceof Collection);
        $this->assertEquals(3, $files->count());
    }

    public function test_gets_directories()
    {
        $dirs = $this->collection->dirs();

        $this->assertTrue($dirs instanceof Collection);
        $this->assertEquals(3, $dirs->count());
    }

    public function test_gets_first_element()
    {
        $this->assertEquals(
            path($this->tempPath.'file1.txt'),
            $this->collection->first()->path()
        );
    }

    public function test_gets_last_element()
    {
        $this->assertEquals(
            path($this->tempPath.'dir1/file11.txt'),
            $this->collection->last()->path()
        );
    }

    public function test_gets_array_of_names()
    {
        $this->assertEquals(
            ['file1.txt', 'file2.txt', 'dir1', 'dir2', 'dir11', 'file11.txt'],
            $this->collection->names()
        );
    }

    public function test_gets_array_of_paths()
    {
        $this->assertEquals(
            [
                path($this->tempPath.'file1.txt'),
                path($this->tempPath.'file2.txt'),
                path($this->tempPath.'dir1'),
                path($this->tempPath.'dir2'),
                path($this->tempPath.'dir1/dir11'),
                path($this->tempPath.'dir1/file11.txt')
            ],
            $this->collection->paths()
        );
    }

    public function test_removes_element_by_path()
    {
        $this->assertTrue($this->collection->contains(path($this->tempPath.'dir1')));

        $this->collection->remove(path($this->tempPath.'dir1'));

        $this->assertEquals(5, $this->collection->count());
        $this->assertFalse($this->collection->contains(path($this->tempPath.'dir1')));
    }

    public function tearDown()
    {
        remove(path(DEMO_DIR.'/temp'));
    }
}
