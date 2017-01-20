<?php namespace Tarsana\IO\Interfaces\Filesystem;

interface Collection {

    /**
     * Adds new items to the collection. If an
     * item already exists It will be ignored.
     *
     * @param Tarsana\IO\Interfaces\Filesystem\AbstractFile|array $item
     * @return self
     */
    public function add($item);

    /**
     * This is called when the path of an item is changed, It updates
     * the paths array by replacing the old path with the new one.
     *
     * @param  string $oldPath
     * @param  string $newPath
     * @return void
     */
    public function updatePath($oldPath, $newPath);

    /**
     * Returns `true` if one of the collection files
     * has the provided path, `false` otherwise.
     *
     * @param  string $path
     * @return boolean
     */
    public function contains($path);

    /**
     * Returns the mumber of elements in the collection.
     *
     * @return int
     */
    public function count();

    /**
     * Returns a new collection containing only files.
     *
     * @return self
     */
    public function files();

    /**
     * Returns a new collection containing only directories.
     *
     * @return self
     */
    public function dirs();

    /**
     * Returns elements as array.
     *
     * @return array
     */
    public function asArray();

    /**
     * Returns the first item or null if the collection is empty.
     *
     * @return Tarsana\IO\Interfaces\Filesystem\AbstractFile|null
     */
    public function first();

    /**
     * Returns the last item or null if the collection is empty.
     *
     * @return Tarsana\IO\Interfaces\Filesystem\AbstractFile|null
     */
    public function last();

    /**
     * Returns array of paths of the items.
     *
     * @return array
     */
    public function paths();

    /**
     * Returns array of names of the items.
     *
     * @return array
     */
    public function names();

    /**
     * Gets a file or directory by path.
     *
     * @param  string $path
     * @return Tarsana\IO\Interfaces\Filesystem\AbstractFile
     */
    public function get($path);

    /**
     * Removes a file or directory from the collection.
     *
     * @param  string $path
     * @return Tarsana\IO\Interfaces\Filesystem\Collection
     */
    public function remove($path);

}
