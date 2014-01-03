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
        is_null($file) and $file = tempnam(sys_get_temp_dir(), 'dump_');
        $this->file = gzopen($file, 'wb9');
    }

    public function __destruct()
    {
        gzclose($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        if (!$this->writable) {
            throw new StoreNotWritableException('Store is not writable');
        }

        return gzwrite($this->file, $data);
    }
}
