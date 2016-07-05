<?php

use Tarsana\IO\Interfaces\WriterInterface;

function path($value)
{
    return str_replace('/', DIRECTORY_SEPARATOR, $value);
}

class WriterMock implements WriterInterface {
    public $content;
    public function write ($content) {
        $this->content = $content;
    }
    public function writeLine ($content) {}
}
