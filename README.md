# Tarsana I/O Package

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/63923f3f-955d-4058-bbd0-4730639709d4/small.png)](https://insight.sensiolabs.com/projects/63923f3f-955d-4058-bbd0-4730639709d4)

[![Build Status](https://travis-ci.org/tarsana/io.svg?branch=master)](https://travis-ci.org/tarsana/io)
[![Coverage Status](https://coveralls.io/repos/github/tarsana/io/badge.svg?branch=master)](https://coveralls.io/github/tarsana/io?branch=master)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](http://opensource.org/licenses/MIT)

Simple classes to handle Input/Output operations.

## Installation

Install it using composer

```
composer require tarsana/io
```

## Handeling Files and Directories

The `Filesystem` class was designed to be used easily and support call chaining which makes the code more readable.

```php
// Create a Filesystem instance given a root path
$fs = new Tarsana\IO\Filesystem('path/to/fs/root/directory');
```

### Checking Paths

Maybe you need to check if a specific path is a file

```php
if ($fs->isFile('relative/path'))
```

or a directory

```php
if ($fs->isDir('relative/path'))
```

or you just want to know it exists, no matter it's a file or directory

```php
if ($fs->isAny('relative/path'))
```

What if you need to check multiple paths at once ?

```php
if ($fs->areFiles(['relative/path1', 'relative/path2', 'relative/path3']))
if ($fs->areDirs(['relative/path1', 'relative/path2', 'relative/path3']))
if ($fs->areAny(['relative/path1', 'relative/path2', 'relative/path3']))
```

But what if you want to know the type of a path without having to do multiple checks ?

```php
$fs->whatIs('path-pattern')
```

You can use wildcard pattern as argument to this function and the result will be:

- `'file'`: if a single file corresponds to the pattern.

- `'dir'`: if a single directory corresponds to the pattern.

- `'collection'`: if multiple files and/or directories correspond to the pattern.

- `'nothing'`: if nothing corresponds to the pattern.

### Finding Files and Directories

Now what if you want to get all files and directories corresponding to a pattern ?

```php
$collection = $fs->find('pattern'); // a Collection instance

foreach ($collection->asArray() as $fileOrDir) {
	// Handle the file or directory
}
```

You can also manipulate the collection

```php
$collection->count(); // number of elements
$collection->add($fs->file('path/to/file')); // add new element to the collection
$collection->contains('path'); // checks if the collection contains an element with that path
$collection->remove('path'); // remove the element having the path from the collection

$collection->files(); // a new collection containing only files
$collection->dirs(); // a new collection containing only directories

$collection->first(); // the first element
$collection->last(); // the last element

$collection->paths(); // array of paths of the files and directories
$collection->names(); // array of names of the files and directories
```

### Handling Files

Well, to handle a file, you should get it first

```php
$file = $fs->file('relative/path/to/file');
```

Notice that this will throw an exception if the file is not found. If you want to create it when missing; specify `true` in the second argument

```php
$file = $fs->file('relative/path/to/file', true);
```

You can also get or create multiple files at once

```php
$files = $fs->files([
	'relative/path/to/file1',
	'relative/path/to/file2',
	'relative/path/to/file3'
]); // specify the second argument as true if you want missing files to be created

foreach ($files->asArray() as $file) {
	// Handle the file
}
```

Now that you have the file, you can play with it

```php
$file->name(); // get the name
$file->name('new-name.txt'); // renaming the file

$file->path(); // get the absolute path
$file->path('new/absolute/path'); // moving the file

$file->content(); // reading the content
$file->content('new content'); // writing to the file
$file->append('additional content'); // add content to the file

$file->hash(); // get the md5 hash of the content
$file->extension(); // get the extension (like "txt" or "php")

$file->perms(); // get the file permissions as string (like "0755")
$file->isWritable(); // check if the file is writable
$file->isExecutable(); // check if the file is executable

$copy = $file->copyAs('absolute/path/to/file-copy'); // Copy the file
$file->remove(); // Remove the file
```

Notice that all setters return the same instance to enable call chaining.

### Handling Directories

Just like the file, you can get a directory like that

```php
$dir = $fs->dir('relative/path/to/dir'); // throws exception if the directory not found
$dir = $fs->dir('relative/path/to/dir', true); // creates the directory if not found
$dirs = $fs->dirs([
	'relative/path/to/file1',
	'relative/path/to/file2',
	'relative/path/to/file3'
]); // a collection containing directories
```

Having a directory, you can play with it
```php
$dir->name(); // get the name
$dir->name('new-name'); // renaming the directory

$dir->path(); // get the absolute path
$dir->path('new/absolute/path'); // moving the directory

$dir->perms(); // get the directory permissions as string (like "0755")

$copy = $dir->copyAs('absolute/path/to/dir-copy'); // Copy the directory
$dir->remove(); // Remove the directory

$dir->fs(); // get a Filesystem instance having this directory as root
```

Notice that all setters return the same instance to enable call chaining.

## Reading & Writing to Resources

### OutputResource

`Tarsana\IO\Resources\OutputResource` gives the possibility to write content to any resource.

```php
// Default constructor uses STDOUT by default
$stdout = new OutputResource;
// Any writable resource can be used
$res = fopen('temp.txt', 'w');
$out = OutputResource($res);
// Or just give the path
$out = OutputResource('php://memory');

// Writing content
$out->write('Hello ')->writeLine('World !');
// Writes "Hello World !\n" to the resource

// The resource is closed when the $out object is destructed
// But you can still close it before
$out->close();
```

### InputResource

`Tarsana\IO\Resources\InputResource` gives the possibility to read content from any resource. Constructors are the same as `OutputResource` but the default resource is `STDIN`.

```php
$stdin = new InputResource; // when no parameter is given, it uses STDIN by default

$stdin->readLine(); // reads a line from STDIN
$stdin->read(); // reads the whole content of STDIN
$stdin->read(100); // reads 100 bytes from STDIN

// If the STDIN is empty and we do
$stdin->blocking(false)->read();
// This will return immediately an empty string; no blocking !

// Piping content to an OutputResource
$stdin->pipe($out); // the whole remaining content in $stdin will
// be written to $out

```

**New: ** `$stdin->stream()` returns a [Lazy Stream](https://github.com/tarsana/functional#streams) to handle data on the resource in a functional lazy way.

### PipeResource

`Tarsana\IO\Resources\PipeResource` is an Input and Output Resource. It has the ability to read and write at the same time.
