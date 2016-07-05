<?php namespace Tarsana\IO\Resources;

use Tarsana\IO\Interfaces\Reader;
use Tarsana\IO\Interfaces\Writer;

/**
 * Reads and writes content to a resource.
 */
class PipeResource extends Resource implements Reader, Writer {

    /**
     * The current writting position in the file.
     *
     * @var int
     */
    protected $writePosition;

    /**
     * The current reading position in the file.
     *
     * @var int
     */
    protected $readPosition;

    /**
     * Creates a new InputResource.
     *
     * @param string|resource $resource
     */
    public function __construct ($resource = null)
    {
        if (null === $resource) {
            $resource = 'php://memory';
        }
        if (is_string($resource)) {
            $resource = fopen($resource, 'r+');
        }
        if (! is_resource($resource)) {
            throw new ResourceException("Invalid resource given to stream");
        }
        $this->resource = $resource;
        $this->readPosition = 0;
        $stat = fstat($resource);
        $this->writePosition = $stat['size'];
    }

    /**
     * Reads content.
     *
     * @param  int $length the max length to read, -1 specifies no limit.
     * @return string
     */
    public function read ($length = -1)
    {
        fseek($this->resource, $this->readPosition);
        $content = stream_get_contents($this->resource, $length);
        $this->readPosition = ftell($this->resource);
        return $content;
    }

    /**
     * Reads one line.
     *
     * @return string
     */
    public function readLine ()
    {
        fseek($this->resource, $this->readPosition);
        $content = fgets($this->resource);
        $this->readPosition = ftell($this->resource);
        return $content;
    }

    /**
     * Writes content.
     *
     * @return self
     */
    public function write ($content)
    {
        fseek($this->resource, $this->writePosition);
        if(false === fwrite($this->resource, $content))
            throw new ResourceException("Unable to write content to stream");
        $this->writePosition = ftell($this->resource);
        return $this;
    }

    /**
     * Writes content and adds EOL.
     *
     * @return self
     */
    public function writeLine ($content)
    {
        return $this->write($content . PHP_EOL);
    }

    /**
     * Pipe all the content to the given writer.
     *
     * @param  Writer $w
     * @return void
     */
    public function pipe(Writer $w)
    {
        $w->write($this->read());
    }
}
