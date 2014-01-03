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
     * File handler
     *
     * @var resource
     */
    protected $file;

    public function __construct($file = null)
    {
        is_null($file) and $file = tempnam(sys_get_temp_dir(), 'dump_');
        $this->file = fopen($file, 'w+');
    }

    public function __destruct()
    {
        fclose($this->file);
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        if (!$this->writable) {
            throw new StoreNotWritableException('Store is not writable');
        }

        return fwrite($this->file, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->readable) {
            throw new StoreNotReadableException('Store is not readable');
        }

        rewind($this->file);
        return stream_get_contents($this->file);
    }
}
