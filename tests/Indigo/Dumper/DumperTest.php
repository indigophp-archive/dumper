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
    public function provider()
    {
        return array(
            array(
                new Dumper(
                    new SqliteConnector(array(
                        'database'                   => __DIR__ . '/../../test.sqlite',
                        'drop_table'                 => true,
                        'drop_view'                  => true,
                        'disable_foreign_keys_check' => true,
                        'use_transaction'            => true,
                    )),
                    new VariableStore
                )
            ),
            array(
                new Dumper(
                    new SqliteConnector(array(
                        'database'                   => __DIR__ . '/../../test.sqlite',
                        'drop_table'                 => false,
                        'drop_view'                  => false,
                        'disable_foreign_keys_check' => false,
                        'use_transaction'            => false,
                    )),
                    new VariableStore
                )
            ),
        );
    }

    /**
     * @dataProvider provider
     */
    public function testStore($dumper)
    {
        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->setStore(new VariableStore)
        );

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Store\\StoreInterface',
            $dumper->getStore()
        );
    }

    /**
     * @dataProvider provider
     */
    public function testDump($dumper)
    {
        $this->assertTrue($dumper->getStore()->isWritable());
        $this->assertTrue(is_bool($dumper->dump()));
        $this->assertFalse($dumper->getStore()->isWritable());
    }

    /**
     * @dataProvider provider
     */
    public function testRead($dumper)
    {
        $this->assertTrue($dumper->getStore()->isReadable());

        $read = $dumper->read();
        $this->assertTrue(is_string($read) or is_null($read));
    }

    /**
     * @dataProvider provider
     */
    public function testReturn($dumper)
    {
        $this->assertNull($dumper->getOption('nothing_here'));
        $this->assertNull($dumper->getConnectorOption('nothing_here'));
        $this->assertTrue(is_string($dumper->getDatabase()));
    }

    /**
     * @dataProvider provider
     */
    public function testIncludeTable($dumper)
    {
        $this->assertFalse($dumper->hasTable());

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->excludeTable('test2')
        );

        $this->assertFalse($dumper->hasTable());
        $this->assertTrue($dumper->isTableExcluded('test2'));

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->includeTable('test')
        );

        $this->assertTrue($dumper->isTableIncluded('test'));

        $this->assertTrue($dumper->hasTable());
    }

    /**
     * @dataProvider provider
     */
    public function testIncludeView($dumper)
    {
        $this->assertFalse($dumper->hasView());

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->excludeView('v_test2')
        );

        $this->assertFalse($dumper->hasView());
        $this->assertTrue($dumper->isViewExcluded('v_test2'));

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->includeView('v_test')
        );

        $this->assertTrue($dumper->isViewIncluded('v_test'));

        $this->assertTrue($dumper->hasView());
    }
}
