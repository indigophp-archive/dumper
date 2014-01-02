<?php
/*
 * This file is part of the Indigo Dump package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dump\Store;

class VariableStore implements StoreInterface
{
    protected $data;

    public function write($data)
    {
        $this->data .= $data;
        return strlen($data);
    }

    public function read()
    {
    	return $this->data;
    }

    public function save()
    {
    	return true;
    }
}
