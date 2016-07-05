<?php namespace Tarsana\IO\Resources;

use Tarsana\IO\Exceptions\ResourceException;


abstract class Resource {

    /**
     * The source of content.
     *
     * @var mixed
     */
    protected $resource;

    /**
     * Sets blocking mode.
     *
     * @param bool $mode
     * @return self
     * @throws Tarsana\IO\Exceptions\ResourceException
     */
    public function blocking ($mode)
    {
        if(stream_set_blocking($this->resource, $mode))
            return $this;
        throw new ResourceException("Unable to set the blocking mode of stream");
    }

    /**
     * Closes the resource.
     *
     * @return self
     */
    public function close ()
    {
        if (is_resource($this->resource))
            fclose($this->resource);
        return $this;
    }

    public function __destruct ()
    {
        $this->close();
    }
}
