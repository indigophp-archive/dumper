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

class GzStore extends TempStore
{
    public function save($file = null)
    {
        is_null($file) and $file = tempnam(sys_get_temp_dir(), 'dump_');
        $gz = gzopen($file, 'wb9');
        gzwrite($gz, $this->read());
        gzclose($gz);
        return $file;
    }
}
