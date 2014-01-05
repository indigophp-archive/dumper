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
 * Sqlite Connector Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class SqliteConnectorTest extends ConnectorTest
{
    public function provider()
    {
        return array(
            array(
                new SqliteConnector(array(
                    'database'                   => __DIR__ . '/../../../test.sqlite',
                    'drop_table'                 => true,
                    'drop_view'                  => true,
                    'disable_foreign_keys_check' => true,
                    'use_transaction'            => true,
                ))
            ),
            array(
                new SqliteConnector(array(
                    'database'                   => __DIR__ . '/../../../test.sqlite',
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
        $connector = new SqliteConnector(array(
            'database'                   => __DIR__ . '/../../../test.sqlite',
            'drop_table'                 => false,
            'drop_view'                  => false,
            'disable_foreign_keys_check' => false,
            'use_transaction'            => false,
        ));

        $this->assertInstanceOf('Indigo\\Dumper\\Connector\\SqliteConnector', $connector);
    }
}
