<?php

use Tarsana\IO\Interfaces\Writer;

function path($value)
{
    return str_replace('/', DIRECTORY_SEPARATOR, $value);
}

class WriterMock implements Writer {
    public $content;
    public function write ($content) {
        $this->content = $content;
    }
    public function writeLine ($content) {}
}
