<?php namespace Tarsana\IO\Resource;

use Tarsana\IO\Interfaces\ReaderInterface;

/**
 * Reads content from a resource.
 */
class Reader extends ResourceHanlder implements ReaderInterface {

    /**
     * Reads a number of chars/bytes from the stream.
     * If `$count == 0`; reads all the available content.
     *
     * @param  int $count
     * @return string
     */
    public function read($count = 0)
    {
        if (0 === $count) {
            $count = -1;
        }
        return stream_get_contents($this->resource, $count);
    }

    /**
     * Gets the default opening mode.
     *
     * @return string
     */
    protected function defaultMode()
    {
        return 'rb';
    }

    /**
     * Gets the default resource.
     *
     * @return string|resource
     */
    protected function defaultResource()
    {
        return STDIN;
    }

    /**
     * Tells if the given resource is readable.
     *
     * @param  resource  $resource
     * @return boolean
     */
    protected function isValid($resource)
    {
        return $this->isReadable($resource);
    }

}
