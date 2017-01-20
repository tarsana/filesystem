<?php

require __DIR__ . '/../vendor/autoload.php';

define('DEMO_DIR', __DIR__.'/demo');

function path($value) {
    return $value;
}

function remove($path) {
    if (is_file($path))
        return unlink($path);
    if (is_dir($path)) {
        $path = rtrim($path, '/');
        $files = glob($path . '/*', GLOB_MARK);
        foreach ($files as $file) {
            remove($file);
        }
        return rmdir($path);
    }
    return false;
}
