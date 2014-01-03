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
 * Store Interface
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
interface StoreInterface
{
    /**
     * Write data to store
     *
     * Should only be called by dumper
     *
     * @param  string  $data
     * @return integer Bytes written
     */
    public function write($data);

    /**
     * Read data from store
     *
     * @return string
     */
    public function read();

    /**
     * Do any further processing, and make store read-only
     *
     * Called automatically
     *
     * @return boolean Success
     */
    public function save();

    /**
     * Check whether store is in writable state
     *
     * @return boolean
     */
    public function isWritable();

    /**
     * Check whether store is in readable state
     *
     * You can make your store write-only
     *
     * @return boolean
     */
    public function isReadable();
}
