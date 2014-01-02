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

interface StoreInterface
{
    public function write($data);

    public function read();

    public function save();
}
