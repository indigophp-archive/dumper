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

/**
 * Gz Store
 *
 * Store file with gzip compression
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class GzStore extends FileStore
{
    protected $readable = false;

    public function __construct($file = null)
    {
        $this->file = $this->makeFile($file);
        $this->handle = gzopen($this->file, 'wb9');
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($data)
    {
        return gzwrite($this->handle, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function doRead()
    {
        return gzdecode(file_get_contents($this->file));
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave()
    {
        $this->readable = true;

        return gzclose($this->handle);
    }
}
