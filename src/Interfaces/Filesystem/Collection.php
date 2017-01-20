<?php namespace Tarsana\IO\Filesystem;

use Tarsana\IO\Filesystem\Directory;
use Tarsana\IO\Filesystem\File;


class Collection {

    /**
     * Array containing the items, it's indexed by the 
     * path of items and holds other informations 
     * in addition to the file instances.
     * 
     * @var array
     */
    protected $items;

    /**
     * Creates a Collection.
     * 
     * @param array $files
     */
    public function __construct($files = false)
    {
        $this->items = [];
        if (is_array($files)) {
            $this->add($files);
        }
    }

    /**
     * Adds new items to the collection. If an
     * item already exists It will be ignored.
     * 
     * @param Tarsana\IO\Filesystem\AbstractFile|array $item
     * @return Tarsana\IO\Filesystem\Collection
     */
    public function add($item)
    {
        if (is_array($item)) {
            foreach ($item as $element) {
                $this->add($element);
            }
        } else if (! $this->contains($item->path())) {
            $this->items[$item->path()] = [
                'instance' => $item,
                'listener_index' => $item->addPathListener([$this, 'updatePath'])
            ];
        }
        return $this;
    }

    /**
     * This is called when the path of an item is changed, It updates 
     * the paths array by replacing the old path with the new one.
     *
     * @param  string $oldPath
     * @param  string $newPath
     * @return void
     */
    public function updatePath($oldPath, $newPath)
    {
        if ($this->contains($oldPath)) {
            $this->items[$newPath] = $this->items[$oldPath];
            unset($this->items[$oldPath]);
        }
    }

    /**
     * Returns TRUE if one of the collection files 
     * has the provided path, FALSE otherwise.
     * 
     * @param  string $path
     * @return boolean
     */
    public function contains($path)
    {
        return array_key_exists($path, $this->items);
    }

    /**
     * Returns the mumber of elements in the collection.
     * 
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Returns a new collection containing only files.
     * 
     * @return Tarsana\IO\Filesystem\Collection
     */
    public function files()
    {
        $filesList = array_filter($this->items, function($item) {
            return ($item['instance'] instanceof File);
        });

        $filesList = array_map(function($item) {
            return $item['instance'];
        }, $filesList);

        return new Collection($filesList);
    }

    /**
     * Returns a new collection containing only directories.
     * 
     * @return Tarsana\IO\Filesystem\Collection
     */
    public function dirs()
    {
        $filesList = array_filter($this->items, function($item) {
            return ($item['instance'] instanceof Directory);
        });

        $filesList = array_map(function($item) {
            return $item['instance'];
        }, $filesList);

        return new Collection($filesList);
    }

    /**
     * Returns elements as array.
     * 
     * @return array
     */
    public function asArray()
    {
        return array_map(function($item) {
            return $item['instance'];
        }, $this->items);
    }

    /**
     * Returns the first item or null if the collection is empty.
     * 
     * @return Tarsana\IO\Filesystem\AbstractFile|null
     */
    public function first()
    {
        $item = reset($this->items);
        if ($item === false) {
            return null;
        }
        return $item['instance'];
    }

    /**
     * Returns the last item or null if the collection is empty.
     * 
     * @return Tarsana\IO\Filesystem\AbstractFile|null
     */
    public function last()
    {
        $item = end($this->items);
        if ($item === false) {
            return null;
        }
        return $item['instance'];
    }

    /**
     * Returns array of paths of the items.
     * 
     * @return array
     */
    public function paths()
    {
        return array_keys($this->items);
    }

    /**
     * Returns array of names of the items.
     * 
     * @return array
     */
    public function names()
    {
        return array_map('basename', array_keys($this->items));
    }

    /**
     * Gets a file or directory by path.
     * 
     * @param  string $path
     * @return Tarsana\IO\Filesystem\AbstractFile
     */
    public function get($path)
    {
        if ($this->contains($path)) {
            return $this->items[$path]['instance'];
        }
        return null;
    }

    /**
     * Removes a file or directory from the collection.
     * 
     * @param  string $path
     * @return Tarsana\IO\Filesystem\Collection
     */
    public function remove($path)
    {
        if ($this->contains($path)) {
            $item = $this->items[$path];
            $item['instance']->removePathListener($item['listener_index']);
            unset($this->items[$path]);
        }
        return $this;
    }

}
