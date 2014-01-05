<?php
/*
 * This file is part of the Indigo Dumper package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dumper\Store;

use Flysystem\Filesystem;

/**
 * Flysystem Store
 *
 * Store data in Flysystem
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FlysystemStore extends AbstractStore
{
    /**
     * Filesystem object
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * File name or path
     *
     * @var string
     */
    protected $name;

    public function __construct(Filesystem $filesystem, $name)
    {
        $this->filesystem = $filesystem;
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        $this->checkWritable();
        $this->filesystem->put($this->name, $data);

        return strlen($data);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        parent::read();

        return $this->filesystem->read($this->name);
    }
}
