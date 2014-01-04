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
abstract class MysqlConnectorTest extends \PHPUnit_Framework_TestCase
{
    protected $connector;

    public function setUp()
    {
        $this->connector = new MysqlConnector(array(
            'database'                   => 'test',
            'username'                   => 'travis',
            'password'                   => '',
            'drop_table'                 => true,
            'drop_view'                  => true,
            'disable_foreign_keys_check' => true,
            'use_transaction'            => true,
        ));
    }

    public function testValidReturns()
    {
        $this->assertEquals('test', $this->connector->getDatabase());
    }
}
