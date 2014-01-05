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
 * Store Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class StoreTest extends \PHPUnit_Framework_TestCase
{
    protected $store;

    public function testWrite()
    {
        $data = $this->store->write('123');

        $this->assertEquals(3, $data);
    }

    public function testRead()
    {
        $this->store->write('test');

        if ($this->store->isReadable()) {
            $data = $this->store->read();
            $this->assertEquals('test', $data);
        }
    }

    /**
     * Every store should be writable at the beginning
     */
    public function testWritable()
    {
        $this->assertTrue($this->store->isWritable());
    }

    /**
     * Should not be writable after save
     *
     * @expectedException OverflowException
     */
    public function testSave()
    {
        $this->store->save();

        $this->assertFalse($this->store->isWritable());
        $this->store->write('123');
    }
}
