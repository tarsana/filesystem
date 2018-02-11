<?php namespace Tarsana\Filesystem\Resource;

use Tarsana\Filesystem\Interfaces\Resource\Buffer as BufferInterface;
use Tarsana\Filesystem\Exceptions\ResourceException;

/**
 * Reads and writes content from/to a resource.
 */
class Buffer extends ResourceHanlder implements BufferInterface {

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
        fseek($this->resource, $this->readPosition);
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
        $this->readPosition = ftell($this->resource);
        return (substr($buffer, -$size) == $end)
            ? substr($buffer, 0, strlen($buffer) - $size)
            : $buffer;
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
            throw new ResourceException("Unable to write content to resource");
        $this->writePosition = ftell($this->resource);
        return $this;
    }

    /**
     * Writes the given text then an end of line character.
     *
     * @param  string $text
     * @return self
     */
    public function writeLine($text)
    {
        return $this->write($text . PHP_EOL);
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
