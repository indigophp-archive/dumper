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

class TempStore implements StoreInterface
{
    protected $tmp;

    public function __construct()
    {
        $this->tmp = fopen('php://temp', 'w');
    }

    public function __destruct()
    {
        fclose($this->tmp);
    }

    public function write($data)
    {
        return fwrite($this->tmp, $data);
    }

    public function read()
    {
        rewind($this->tmp);
        return stream_get_contents($this->tmp);
    }

    public function save($file = null)
    {
        is_null($file) and $file = tempnam(sys_get_temp_dir(), 'dump_');
        file_put_contents($file, $this->read());
        return $file;
    }
}
