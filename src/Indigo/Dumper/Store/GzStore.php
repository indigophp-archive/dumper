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

use Indigo\Dumper\Exception\StoreNotWritableException;

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
        $this->file = $this->file($file);
        $this->handle = gzopen($this->file, 'wb9');
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        if (!$this->writable) {
            throw new StoreNotWritableException('Store is not writable');
        }

        return gzwrite($this->handle, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        gzclose($this->handle);
        return parent::save();
    }
}
