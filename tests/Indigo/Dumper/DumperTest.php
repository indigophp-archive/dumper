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
use Indigo\Dumper\Connector\MysqlConnector;
use Indigo\Dumper\Store\VariableStore;

/**
 * Dumper Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class DumperTest extends \PHPUnit_Framework_TestCase
{
    protected $dumper;

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
            array(
                new Dumper(
                    new MysqlConnector(array(
                        'database'                   => 'test',
                        'username'                   => 'travis',
                        'password'                   => '',
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
                    new MysqlConnector(array(
                        'database'                   => 'test',
                        'username'                   => 'travis',
                        'password'                   => '',
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

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testInstance()
    {
        $connector = \Mockery::mock('Indigo\\Dumper\\Connector\\ConnectorInterface');
        $store = \Mockery::mock('Indigo\\Dumper\\Store\\StoreInterface', function ($mock) {
            $mock->shouldReceive('getFile')
                ->andReturn(tempnam(sys_get_temp_dir(), ''));
        });

        $dumper = new Dumper($connector, $store);

        $this->assertInstanceOf('Indigo\\Dumper\\Dumper', $dumper);
    }

    /**
     * @dataProvider provider
     */
    public function testStore($dumper)
    {
        $store = \Mockery::mock('Indigo\\Dumper\\Store\\StoreInterface', function ($mock) {
            $mock->shouldReceive('getFile')
                ->andReturn(tempnam(sys_get_temp_dir(), ''));
        });

        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->setStore($store)
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
        $this->assertTrue(is_bool($dumper->getOption('no_data')));
        $this->assertTrue(is_array($dumper->getOption()));
        $this->assertTrue(is_string($dumper->getDatabase()));
    }

    /**
     * @dataProvider provider
     */
    public function testIncludeTable($dumper)
    {
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
    public function testExcludeTable($dumper)
    {
        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->excludeTable(array('test2'))
        );

        $this->assertTrue($dumper->isTableExcluded('test2'));
    }

    /**
     * @dataProvider provider
     * @expectedException InvalidArgumentException
     */
    public function testIncludeTableFailure($dumper)
    {
        $dumper->includeTable(null);
    }

    /**
     * @dataProvider provider
     */
    public function testIncludeView($dumper)
    {
        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->includeView('v_test')
        );

        $this->assertTrue($dumper->isViewIncluded('v_test'));

        $this->assertTrue($dumper->hasView());
    }

    /**
     * @dataProvider provider
     */
    public function testExcludeView($dumper)
    {
        $this->assertInstanceOf(
            'Indigo\\Dumper\\Dumper',
            $dumper->excludeView(array('v_test2'))
        );

        $this->assertTrue($dumper->isViewExcluded('v_test2'));
    }

    /**
     * @dataProvider provider
     * @expectedException InvalidArgumentException
     */
    public function testIncludeViewFailure($dumper)
    {
        $dumper->includeView(null);
    }
}
