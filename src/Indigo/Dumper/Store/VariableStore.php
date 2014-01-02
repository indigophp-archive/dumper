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

class VariableStore implements AbstractStore
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
        parent::write($data);
        $this->data .= $data;
        return strlen($data);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        parent::read();
        return $this->data;
    }
}
