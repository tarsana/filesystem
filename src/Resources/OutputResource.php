<?php namespace Tarsana\IO\Resources;

use Tarsana\IO\Exceptions\ResourceException;
use Tarsana\IO\Interfaces\WriterInterface;

/**
 * Writes content to a resource.
 */
class OutputResource extends Resource implements WriterInterface {

    /**
     * Creates a new OutputResource.
     *
     * @param string|resource $resource
     */
    public function __construct ($resource = null)
    {
        if (null === $resource) {
            $resource = STDOUT;
        }
        if (is_string($resource)) {
            $resource = fopen($resource, 'a');
        }
        if (! is_resource($resource)) {
            throw new ResourceException("Invalid resource given to stream");
        }
        $this->resource = $resource;
    }

    /**
     * Writes content.
     *
     * @return self
     */
    public function write ($content)
    {
        if(false === fwrite($this->resource, $content))
            throw new ResourceException("Unable to write content to stream");
        return $this;
    }

    /**
     * Writes content and adds EOL.
     *
     * @return self
     */
    public function writeLine($content)
    {
        $this->write($content . PHP_EOL);
    }

    /**
     * Returns a Stream of the resource content.
     *
     * @return Tarsana\Functional\Stream
     */
    public function stream()
    {
        return Stream::of($this);
    }

}
