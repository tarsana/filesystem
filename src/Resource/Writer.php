<?php namespace Tarsana\Filesystem\Resource;

use Tarsana\Filesystem\Interfaces\Resource\Writer as WriterInterface;
use Tarsana\Filesystem\Exceptions\ResourceException;

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
            throw new ResourceException("Unable to write content to resource");
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
        return 'php://stdout';
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
