<?php
/*
 * This file is part of the Indigo Dumper package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dumper;

use Indigo\Dumper\Connector\SqliteConnector;
use Indigo\Dumper\Store\VariableStore;

/**
 * Dumper Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class DumperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Dumper object
     *
     * @var Dumper
     */
    protected $dumper;

    public function setUp()
    {
        $options = array(
            'database' => __DIR__ . '/../../test.sqlite'
        );

        $connector = new SqliteConnector($options);
        $store = new VariableStore;

        $this->dumper = new Dumper($connector, $store);
    }

    public function testStore()
    {
        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $this->dumper->setStore(new VariableStore)
        );

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Store\\StoreInterface',
            $this->dumper->getStore()
        );
    }

    public function testDump()
    {
        $this->assertTrue($this->dumper->getStore()->isWritable());
        $this->assertTrue(is_bool($this->dumper->dump()));
        $this->assertFalse($this->dumper->getStore()->isWritable());
    }

    public function testRead()
    {
        $this->assertTrue($this->dumper->getStore()->isReadable());

        $read = $this->dumper->read();
        $this->assertTrue(is_string($read) or is_null($read));
    }

    public function testReturn()
    {
        $this->assertNull($this->dumper->getOption('nothing_here'));
        $this->assertNull($this->dumper->getConnectorOption('nothing_here'));
        $this->assertTrue(is_string($this->dumper->getDatabase()));
    }

    public function testIncludeTable()
    {
        $this->assertFalse($this->dumper->hasTable());

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $this->dumper->excludeTable('test2')
        );

        $this->assertFalse($this->dumper->hasTable());
        $this->assertTrue($this->dumper->isTableExcluded('test2'));

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $this->dumper->includeTable('test')
        );

        $this->assertTrue($this->dumper->isTableIncluded('test'));

        $this->assertTrue($this->dumper->hasTable());
    }

    public function testIncludeView()
    {
        $this->assertFalse($this->dumper->hasView());

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $this->dumper->excludeView('v_test2')
        );

        $this->assertFalse($this->dumper->hasView());
        $this->assertTrue($this->dumper->isViewExcluded('v_test2'));

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $this->dumper->includeView('v_test')
        );

        $this->assertTrue($this->dumper->isViewIncluded('v_test'));

        $this->assertTrue($this->dumper->hasView());
    }
}
