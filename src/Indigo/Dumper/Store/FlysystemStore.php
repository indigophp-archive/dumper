<?php
/*
 * This file is part of the Indigo Dump package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dumper\Store;

use Flysystem\Filesystem;

class FlysystemStore implements StoreInterface
{
    protected $filesystem;
    protected $name;

    public function __construct(Filesystem $filesystem, $name)
    {
        $this->filesystem = $filesystem;
        $this->name = $name;
    }

    public function write($data)
    {
        $this->filesystem->write($this->name, $data);
        return strlen($data);
    }

    public function read()
    {
        return $this->filesystem->read($this->name);
    }

    public function save()
    {
        return true;
    }
}
