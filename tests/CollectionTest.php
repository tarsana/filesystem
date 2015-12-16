<?php
require_once __DIR__ . '/utils.php';

use Tarsana\IO\Filesystem\Collection;
use Tarsana\IO\Filesystem\Directory;
use Tarsana\IO\Filesystem\File;


class CollectionTest extends PHPUnit_Framework_TestCase {

    protected $collection;

    protected $tempPath;

    public function setUp()
    {
        $this->tempPath = path(__DIR__.'/demo/temp/');
        $this->collection = new Collection([
            new File(path($this->tempPath.'file1.txt')),
            new File(path($this->tempPath.'file2.txt')),
            new Directory(path($this->tempPath.'dir1')),
            new Directory(path($this->tempPath.'dir2')),
            new Directory(path($this->tempPath.'dir1/dir11')),
            new File(path($this->tempPath.'dir1/file11.txt'))
        ]);
    }

    public function testAddingAndCountingElements()
    {
        $this->assertEquals(6, $this->collection->count());

        $this->collection->add(new File(path($this->tempPath.'file3.txt')));
        $this->assertEquals(7, $this->collection->count());

        // Does not add the same file twice 
        $this->collection->add(new File(path($this->tempPath.'file1.txt')));
        $this->assertEquals(7, $this->collection->count());
    }

    public function testContains()
    {
        $this->assertTrue($this->collection->contains(path($this->tempPath.'file1.txt')));
        $this->assertFalse($this->collection->contains(path($this->tempPath.'file.txt')));
    }

    public function testGetsFileOrDirectoryByPath()
    {
        $file = $this->collection->get(path($this->tempPath.'file1.txt'));
        $this->assertTrue($file instanceof File);
        $this->assertEquals('file1.txt', $file->name());
    }

    public function testUpdatesPathsAutomatically()
    {
        $file = $this->collection->get(path($this->tempPath.'file1.txt'));
        $this->assertFalse($this->collection->contains(path($this->tempPath.'other.txt')));
        $file->name('other.txt', true);
        $this->assertTrue($this->collection->contains(path($this->tempPath.'other.txt')));
    }
    
    public function testGetsAll()
    {
        $array = $this->collection->asArray();

        $this->assertTrue(is_array($array));
        $this->assertEquals(6, count($array));
    }
    
    public function testGetsFiles()
    {
        $files = $this->collection->files();

        $this->assertTrue($files instanceof Collection);
        $this->assertEquals(3, $files->count());
    }
    
    public function testGetsDirectories()
    {
        $dirs = $this->collection->dirs();

        $this->assertTrue($dirs instanceof Collection);
        $this->assertEquals(3, $dirs->count());
    }
    
    public function testGetsFirstElement()
    {
        $this->assertEquals(
            path($this->tempPath.'file1.txt'),
            $this->collection->first()->path()
        );
    }
    
    public function testGetsLastElement()
    {
        $this->assertEquals(
            path($this->tempPath.'dir1/file11.txt'),
            $this->collection->last()->path()
        );
    }

    public function testGetsArrayOfNames()
    {
        $this->assertEquals(
            ['file1.txt', 'file2.txt', 'dir1', 'dir2', 'dir11', 'file11.txt'],
            $this->collection->names()
        );
    }

    public function testGetsArrayOfPaths()
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
    
    public function testRemovesElementByPath()
    {
        $this->assertTrue($this->collection->contains(path($this->tempPath.'dir1')));
        
        $this->collection->remove(path($this->tempPath.'dir1'));

        $this->assertEquals(5, $this->collection->count());
        $this->assertFalse($this->collection->contains(path($this->tempPath.'dir1')));
    }

    public function tearDown()
    {
        shell_exec('rm -rf ' . path(__DIR__.'/demo/temp'));
    }
}
