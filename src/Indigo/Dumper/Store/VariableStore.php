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
 * Variable Store
 *
 * Store data in variable
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class VariableStore extends AbstractStore
{
    /**
     * Data
     *
     * @var string
     */
    protected $data;

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        if (!$this->writable) {
            throw new \RuntimeException('Store is not writable');
        }

        $this->data .= $data;
        return strlen($data);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        if (!$this->readable) {
            throw new \RuntimeException('Store is not readable');
        }

        return $this->data;
    }
}
