<?php namespace Tarsana\Filesystem\Resource;

use Tarsana\Filesystem\Exceptions\ResourceException;
use Tarsana\Filesystem\Interfaces\Resource\Reader as ReaderInterface;

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
     * Reads until end of line or the end of contents.
     * Returns the read string without the end of line.
     *
     * @return string
     */
    public function readLine()
    {
        return $this->readUntil(PHP_EOL);
    }

    /**
     * Reads until the given string or the end of contents.
     * Returns the read string without the ending string.
     *
     * @param  string $end
     * @return string
     * @throws ResourceException if the given $end is empty
     */
    public function readUntil($end)
    {
        if (empty($end)) {
            throw new ResourceException("Empty string given to Reader::readUntil()");
        }
        // Reading the first character (maybe blocking)
        $buffer = stream_get_contents($this->resource, 1);
        // saving the blocking mode
        $mode = $this->blocking();
        $size = strlen($end);
        $this->blocking(false);
        while (strlen($buffer) < $size || substr($buffer, -$size) != $end) {
            $c = stream_get_contents($this->resource, 1);
            if (empty($c)) {
                break;
            }
            $buffer .= $c;
        }
        $this->blocking($mode);
        return (substr($buffer, -$size) == $end)
            ? substr($buffer, 0, strlen($buffer) - $size)
            : $buffer;
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
        return 'php://stdin';
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
