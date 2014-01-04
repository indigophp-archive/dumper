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
use Indigo\Dumper\Exception\StoreNotReadableException;

/**
 * File Store
 *
 * Store file without compression
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FileStore extends AbstractStore
{
    /**
     * File path
     *
     * @var string
     */
    protected $file;

    /**
     * File handle
     *
     * @var resource
     */
    protected $handle;

    public function __construct($file = null)
    {
        $this->file = $this->file($file);
        $this->handle = fopen($this->file, 'w+');
    }

    public function __destruct()
    {
        @fclose($this->handle);
    }

    /**
     * Get file
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Get or create file
     *
     * @param  string $name
     * @return string
     */
    protected function file($name)
    {
        if ($path = dirname($name) and $path !== '.') {
            $path = realpath($path);
        } else {
            $path = sys_get_temp_dir();
        }

        if ($name = basename($name)) {
            return $path  . '/' . $name;
        } else {
            return tempnam($path, 'dump_');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        if (!$this->writable) {
            throw new StoreNotWritableException('Store is not writable');
        }

        return fwrite($this->handle, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->readable) {
            throw new StoreNotReadableException('Store is not readable');
        }

        rewind($this->handle);

        return stream_get_contents($this->handle);
    }
}
