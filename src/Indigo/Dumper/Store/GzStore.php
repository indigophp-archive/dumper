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

class GzStore extends FileStore
{
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
            throw new \RuntimeException('Store is not writable');
        }

        return gzwrite($this->file, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->readable) {
            throw new \RuntimeException('Store is not readable');
        }

        gzrewind($this->file);

        $read = '';

        while (!gzeof($this->file)) {
            $read .= gzread($this->file, 2048);
        }

        return $read;
    }
}
