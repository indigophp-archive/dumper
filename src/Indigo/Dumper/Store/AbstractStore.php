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
    public function write($data)
    {
        if (!$this->writable) {
            throw new StoreNotWritableException('Store is not writable');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->readable) {
            throw new StoreNotReadableException('Store is not readable');
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
