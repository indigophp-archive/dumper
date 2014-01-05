<?php
/*
 * This file is part of the Indigo Dumper package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dumper\Connector;

/**
 * Mysql Connector Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class MysqlConnectorTest extends ConnectorTest
{
    public function provider()
    {
        return array(
            array(
                new MysqlConnector(array(
                    'database'                   => 'test',
                    'username'                   => 'travis',
                    'password'                   => '',
                    'drop_table'                 => true,
                    'drop_view'                  => true,
                    'disable_foreign_keys_check' => true,
                    'use_transaction'            => true,
                ))
            ),
            array(
                new MysqlConnector(array(
                    'database'                   => 'test',
                    'username'                   => 'travis',
                    'password'                   => '',
                    'drop_table'                 => false,
                    'drop_view'                  => false,
                    'disable_foreign_keys_check' => false,
                    'use_transaction'            => false,
                ))
            )
        );
    }

    public function testInstance()
    {
        $connector = new MysqlConnector(array(
            'database'                   => 'test',
            'username'                   => 'travis',
            'password'                   => '',
            'drop_table'                 => true,
            'drop_view'                  => true,
            'disable_foreign_keys_check' => true,
            'use_transaction'            => true,
        ));

        $this->assertInstanceOf('Indigo\\Dumper\\Connector\\MysqlConnector', $connector);
    }

    /**
     * @expectedException PDOException
     */
    public function testUnixPath()
    {
        $connector = new MysqlConnector(array(
            'database'                   => 'test',
            'username'                   => 'travis',
            'password'                   => '',
            'unix_socket'                => '/path/to/socket',
        ));
    }

    /**
     * @dataProvider provider
     */
    public function testValidReturns($connector)
    {
        $this->assertEquals('test', $connector->getDatabase());
    }
}
