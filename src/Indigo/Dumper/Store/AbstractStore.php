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

use Indigo\Dumper\Exception\StoreNotReadableException;

/**
 * Abstract Store
 *
 * Basic functions
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class AbstractStore implements StoreInterface
{
    /**
     * Is store writable?
     *
     * @var boolean
     */
    protected $writable = true;

    /**
     * Is store readable?
     *
     * @var boolean
     */
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
    final public function write($data)
    {
        if (!$this->writable) {
            throw new \OverflowException('Store is not writable');
        }

        return $this->doWrite($data);
    }

    /**
     * {@inheritdoc}
     */
    final public function read()
    {
        if (!$this->readable) {
            throw new StoreNotReadableException('Store is not readable');
        }

        return $this->doRead();
    }

    /**
     * {@inheritdoc}
     */
    final public function save()
    {
        $this->writable = false;

        return $this->doSave();
    }

    /**
     * Internal function to write data to store
     *
     * Should only be called by dumper
     *
     * @param  string  $data
     * @return integer Bytes written
     */
    abstract protected function doWrite($data);

    /**
     * Internal function to read data from store
     *
     * @return string
     */
    abstract protected function doRead();

    /**
     * Do any further processing
     *
     * @return boolean Success
     */
    protected function doSave()
    {
        return true;
    }
}
