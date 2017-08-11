<?php namespace Tarsana\IO\Filesystem\Adapters;

use Tarsana\IO\Interfaces\Filesystem\Adapter;


class Memory implements Adapter {

    /**
     * Associative array of files and directories.
     * [
     *  'path-to-file' => [
     *    type => 'file',
     *    content => '...',
     *    perms => 0777
     *  ],
     *  'path-to-dir' => [
     *    type => 'dir',
     *    perms => 0755
     *  ]
     * ]
     *
     * @var array
     */
    protected $files;

    /**
     * Creates a new Memory filesystem adapter.
     */
    public function __construct()
    {
        $this->files = [];
    }

    /**
     * gets or sets the file or directory at `$path`.
     *
     * @param  string $path
     * @param  array $value
     * @return stdClass|null|self
     */
    protected function at($path, $value = false)
    {
        $path = $this->realpath($path);

        if (false === $value) {
            if (isset($this->files[$path]))
                return $this->files[$path];
            else
                return null;
        }

        if (null === $value) {
            unset($this->files[$path]);
            return $this;
        }

        $this->files[$path] = (object) $value;
        return $this;
    }

    /**
     * Makes the path absolute and removes '.'
     * and '..' directories from it.
     *
     * @param  string $path
     * @return string
     */
    public function realpath($path)
    {
        $path = str_replace('\\', '/', rtrim($path, '/'));
        if ($path[0] != '/' && (count($path) < 2 || $path[1] != ':')) {
            $path = getcwd() . '/' . $path;
        }
        $parts = explode('/', $path);
        $path = [];
        foreach ($parts as $part) {
            if ($part == '..') {
                array_pop($path);
            } else if ($part != '.') {
                array_push($path, $part);
            }
        }
        return implode('/', $path);
    }

    /**
     * Equivalent to PHP `glob` function.
     *
     * @param  string $pattern
     * @return array
     */
    public function glob($pattern)
    {
        $matched = [];
        $pattern = $this->realpath($pattern);
        $pattern = '@^' . str_replace(['\*', '\?'], ['[^\\\/]*', '.'], preg_quote($pattern)) .'$@';
        foreach (array_keys($this->files) as $path) {
            if (preg_match($pattern, $path))
                $matched[] = $path;
        }
        return $matched;
    }

    /**
     * Equivalent to PHP `is_file` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isFile($path)
    {
        $node = $this->at($path);
        return null !== $node && 'file' === $node->type;
    }

    /**
     * Equivalent to PHP `is_dir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isDir($path)
    {
        $node = $this->at($path);
        return null !== $node && 'dir' === $node->type;
    }

    /**
     * Equivalent to PHP `file_exists` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function fileExists($path)
    {
        return null !== $this->at($path);
    }

    /**
     * Equivalent to PHP `md5_file` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function md5File($path)
    {
        if (! $this->isFile($path))
            return false;
        return md5($this->at($path)->content);
    }

    /**
     * Equivalent to PHP `file_get_contents` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function fileGetContents($path)
    {
        if (! $this->isFile($path))
            return false;
        return $this->at($path)->content;
    }

    /**
     * Equivalent to PHP `file_put_contents` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function filePutContents($path, $content, $flags = 0)
    {
        if (! $this->isFile($path) && !$this->createFile($path))
            return false;
        if (($flags & FILE_APPEND) == FILE_APPEND)
            $this->at($path)->content .= $content;
        else
            $this->at($path)->content = $content;
        return true;
    }

    /**
     * Equivalent to PHP `is_readable` function.
     * Assumes that the user is the file owner.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isReadable($path)
    {
        $node = $this->at($path);
        return null !== $node && (0x0100 & $node->perms);
    }

    /**
     * Equivalent to PHP `is_writable` function.
     * Assumes that the user is the file owner.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isWritable($path)
    {
        $node = $this->at($path);
        return null !== $node && (0x0080 & $node->perms);
    }

    /**
     * Equivalent to PHP `is_executable` function.
     * Assumes that the user is the file owner.
     *
     * @param  string  $path
     * @return boolean
     */
    public function isExecutable($path)
    {
        $node = $this->at($path);
        return null !== $node
            && (0x0040 & $node->perms)
            && !(0x0800 & $node->perms);
    }

    /**
     * Equivalent to PHP `unlink` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function unlink($path)
    {
        $this->at($path, null);
        return true;
    }

    /**
     * Equivalent to PHP `rmdir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function rmdir($path)
    {
        $path = $this->realpath($path) . '/';
        $length = strlen($path);
        foreach (array_keys($this->files) as $filename) {
            if (substr($filename, 0, $length) === $path) {
                return false;
            }
        }
        $this->at($path, null);
        return true;
    }

    /**
     * Equivalent to PHP `rename` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function rename($oldPath, $newPath)
    {
        $old = $this->at($oldPath);
        $new = $this->at($newPath);

        if (null === $old || (null !== $new && 'dir' === $new->type))
            return false;

        if ('file' === $old->type) {
            $this->at($newPath, $old);
        } else {
            if (! $this->mkdir($newPath, $old->perms)) {
                return false;
            }
            $oldPath = $this->realpath($oldPath) . '/';
            $newPath = $this->realpath($newPath) . '/';
            $length = strlen($oldPath);
            foreach (array_keys($this->files) as $childPath) {
                if (substr($childPath, 0, $length) === $oldPath) {
                    $newChildPath = $newPath . substr($childPath, $length);
                    $this->at($newChildPath, $this->at($childPath));
                    $this->at($childPath, null);
                }
            }
        }
        $this->at($oldPath, null);
        return true;
    }

    /**
     * Equivalent to PHP `fileperms` function.
     *
     * @param  string  $path
     * @return int
     */
    public function fileperms($path)
    {
        $node = $this->at($path);
        if (null === $node) {
            return false;
        }
        return $node->perms;
    }

    /**
     * Equivalent to PHP `chmod` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function chmod($path, $value)
    {
        $node = $this->at($path);
        if (null === $node || !is_numeric($value)) {
            return false;
        }
        $node->perms = $value;
        return true;
    }

    /**
     * Equivalent to PHP `clearstatcache` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function clearstatcache($clearRealCache, $path)
    {
        // Nothing to do as there is no cache !
    }

    /**
     * Equivalent to PHP `mkdir` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function mkdir($path, $mode = 0777, $recursive = false)
    {
        if (null !== $this->at($path)) {
            trigger_error("mkdir(): File exists");
            return false;
        }

        $parts = explode('/', $this->realpath($path));
        $filesCount = 0;
        $missing = [];
        $item = array_shift($parts);
        foreach ($parts as $part) {
            $item .= "/{$part}";
            $node = $this->at($item);
            if ($node === null) {
                $missing[] = $item;
            } else if ($node->type == 'file') {
                $filesCount ++;
            }
        }

        if ($filesCount > 0) {
            trigger_error("mkdir(): Not a directory");
            return false;
        }

        if (count($missing) > 1 && !$recursive) {
            trigger_error("mkdir(): No such file or directory");
            return false;
        }

        foreach ($missing as $path) {
            $this->at($path, (object) [
                'type' => 'dir',
                'perms' => $mode
            ]);
        }

        return true;
   }

    /**
     * Creates a new empty file, overrite.
     *
     * @param  string  $path
     * @return boolean
     */
    public function createFile($path)
    {
        $node = $this->at($path);
        if ($node !== null && $node->type == 'dir') {
            return false;
        }

        $node = $this->at(dirname($path));
        if (null === $node) {
            return false;
        }

        $this->at($path, (object) [
            'type' => 'file',
            'perms' => 0664,
            'content' => ''
        ]);
        return true;
    }

    /**
     * Gets the extension of `$path`.
     *
     * @param  string  $path
     * @return boolean
     */
    public function extension($path)
    {
        $index = strrpos($path, '.');
        return (false === $index) ? '' : substr($path, $index + 1);
    }

    /**
     * Equivalent to PHP `basename` function.
     *
     * @param  string  $path
     * @return boolean
     */
    public function basename($path)
    {
        return basename($path);
    }

}
