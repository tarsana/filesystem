<?php namespace Tarsana\IO\Resource;

use Tarsana\IO\Exceptions\ResourceHandlerException;

/**
 * Abstract class offering basic methods to handle a resource.
 */
abstract class ResourceHanlder {

    /**
     * The resource to handle.
     *
     * @var resource
     */
    protected $resource;

    /**
     * Creates a new ResourceHandler given a resource, a path or
     * URL, if the first argument is a string (a path or URL) the
     * second argument specifies the mode to use to open it if provied
     *
     * @param string|resource $resource
     * @param string $mode
     */
    public function __construct($resource = null, $mode = null)
    {
        if (null === $resource) {
            $resource = $this->defaultResource();
        }
        if (is_string($resource)) {
            if (null === $mode) {
                $mode = $this->defaultMode();
            }
            $resource = fopen($resource, $mode);
        }
        if (!is_resource($resource) || !$this->isValid($resource)) {
            throw new ResourceHandlerException("Invalid resource given to handler");
        }
        $this->resource = $resource;
    }

    /**
     * Sets blocking mode.
     *
     * @param bool $mode
     * @return self
     * @throws Tarsana\IO\Exceptions\ResourceException
     */
    public function blocking($mode)
    {
        if(stream_set_blocking($this->resource, $mode))
            return $this;
        throw new ResourceHandlerException("Unable to set the blocking mode of resource");
    }

    /**
     * Closes the resource.
     *
     * @return self
     */
    public function close()
    {
        if (is_resource($this->resource))
            fclose($this->resource);
        return $this;
    }

    /**
     * Ensures the resource is closed.
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * Tells if the given resource was opened in a read mode.
     * @param  resource  $resource
     * @return boolean
     */
    protected function isReadable($resource)
    {
        $data = stream_get_meta_data($resource);
        return in_array($data['mode'], [
            'r', 'r+', 'w+', 'a+', 'x+', 'c+',
            'rb', 'r+b', 'w+b', 'a+b', 'x+b', 'c+b'
        ]);
    }

    /**
     * Tells if the given resource was opened in a write mode.
     * @param  resource  $resource
     * @return boolean
     */
    protected function isWritable($resource)
    {
        $data = stream_get_meta_data($resource);
        return in_array($data['mode'], [
            'r+', 'w', 'w+', 'a', 'a+', 'x', 'x+', 'c', 'c+',
            'r+b', 'wb', 'w+b', 'ab', 'a+b', 'xb', 'x+b', 'cb', 'c+b'
        ]);
    }

    /**
     * Gets the default opening mode of the handler.
     *
     * @return string
     */
    abstract protected function defaultMode();

    /**
     * Gets the default resource of the handler.
     *
     * @return string|resource
     */
    abstract protected function defaultResource();

    /**
     * Tells if the given resource is valid for the handler.
     *
     * @param  resource  $resource
     * @return boolean
     */
    abstract protected function isValid($resource);

}
