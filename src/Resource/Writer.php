<?php namespace Tarsana\IO\Resource;

use Tarsana\IO\Exceptions\ResourceHandlerException;
use Tarsana\IO\Interfaces\WriterInterface;

/**
 * Writes content to a resource.
 */
class Writer extends ResourceHanlder implements WriterInterface {

    /**
     * Writes content.
     *
     * @return self
     */
    public function write($content)
    {
        if(false === fwrite($this->resource, $content))
            throw new ResourceHandlerException("Unable to write content to resource");
        return $this;
    }

    /**
     * Gets the default opening mode.
     *
     * @return string
     */
    protected function defaultMode()
    {
        return 'r+b';
    }

    /**
     * Gets the default resource.
     *
     * @return string|resource
     */
    protected function defaultResource()
    {
        return STDOUT;
    }

    /**
     * Tells if the given resource is readable.
     *
     * @param  resource  $resource
     * @return boolean
     */
    protected function isValid($resource)
    {
        return $this->isWritable($resource);
    }

}
