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
 * Gz Store Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class GzStoreTest extends FileStoreTest
{
    public function setUp()
    {
        $this->store = new GzStore;
    }

    /**
     * @expectedException Indigo\Dumper\Exception\StoreNotReadableException
     */
    public function testNotReadable()
    {
        $this->store->read();
    }

    /**
     * @expectedException OverflowException
     */
    public function testRead()
    {
        $this->store->write('test');
        $this->store->save();
        $this->assertTrue($this->store->isReadable());

        if ($this->store->isReadable()) {
            $data = $this->store->read();
            $this->assertEquals('test', $data);
        }

        $this->store->write('test');
    }
}
