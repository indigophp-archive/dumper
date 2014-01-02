<?php<?php
/*
 * This file is part of the Indigo Dump package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dumper\Store;

class AbstractStore implements StoreInterface
{
    protected $writable = false;

    protected $readable = true;

    /**
     * {@inheritdoc}
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        if (!$this->writable) {
            throw new \RuntimeException('Store is not writable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->readable) {
            throw new \RuntimeException('Store is not readable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        $this->writable = false;
        return true;
    }
}
