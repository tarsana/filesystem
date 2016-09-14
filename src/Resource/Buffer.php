<?php namespace Tarsana\IO\Resource;

use Tarsana\IO\Interfaces\ReaderInterface;
use Tarsana\IO\Interfaces\WriterInterface;
use Tarsana\IO\Exceptions\ResourceHandlerException;

/**
 * Reads and writes content from/to a resource.
 */
class Buffer extends ResourceHanlder implements ReaderInterface, WriterInterface {

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
     * Creates a new Buffer given a resource, a path or URL,
     * If the first argument is a string (a path or URL) the
     * second argument specifies the mode to use to open it.
     *
     * @param string|resource $resource
     * @param string $mode
     */
    public function __construct($resource = null, $mode = null)
    {
        parent::__construct($resource, $mode);
        $this->readPosition = 0;
        $stat = fstat($this->resource);
        $this->writePosition = $stat['size'];
    }

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
        fseek($this->resource, $this->readPosition);
        $content = stream_get_contents($this->resource, $count);
        $this->readPosition = ftell($this->resource);
        return $content;
    }

    /**
     * Writes content.
     *
     * @return self
     */
    public function write($content)
    {
        fseek($this->resource, $this->writePosition);
        if(false === fwrite($this->resource, $content))
            throw new ResourceHandlerException("Unable to write content to resource");
        $this->writePosition = ftell($this->resource);
        return $this;
    }

    /**
     * Gets the default opening mode of the handler.
     *
     * @return string
     */
    protected function defaultMode()
    {
        return 'a+b';
    }

    /**
     * Gets the default resource of the handler.
     *
     * @return string|resource
     */
    protected function defaultResource()
    {
        return 'php://memory';
    }

    /**
     * Tells if the given resource is valid for the handler.
     *
     * @param  resource  $resource
     * @return boolean
     */
    protected function isValid($resource)
    {
        return $this->isReadable($resource) && $this->isWritable($resource);
    }

}
