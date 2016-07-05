<?php namespace Tarsana\IO\Resources;

use Tarsana\IO\Exceptions\ResourceException;
use Tarsana\IO\Interfaces\ReaderInterface;
use Tarsana\IO\Interfaces\WriterInterface;

/**
 * Reads content from a resource.
 */
class InputResource extends Resource implements ReaderInterface {

    /**
     * Creates a new InputResource.
     *
     * @param string|resource $resource
     */
    public function __construct ($resource = null)
    {
        if (null === $resource) {
            $resource = STDIN;
        }
        if (is_string($resource)) {
            $resource = fopen($resource, 'r');
        }
        if (! is_resource($resource)) {
            throw new ResourceException("Invalid resource given to stream");
        }
        $this->resource = $resource;
    }

    /**
     * Reads content.
     *
     * @param  int $length the max length to read, -1 specifies no limit.
     * @return string
     */
    public function read ($length = -1)
    {
        return stream_get_contents($this->resource, $length);
    }

    /**
     * Reads one line.
     *
     * @return string
     */
    public function readLine ()
    {
        return fgets($this->resource);
    }

    /**
     * Pipe all the content to the given writer.
     *
     * @param  WriterInterface $w
     * @return void
     */
    public function pipe(WriterInterface $w)
    {
        $w->write($this->read());
    }
}
