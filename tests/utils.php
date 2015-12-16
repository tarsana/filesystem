<?php

function path($value)
{
    return str_replace('/', DIRECTORY_SEPARATOR, $value);
}
