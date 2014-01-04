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
 * Connector Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class ConnectorTest extends \PHPUnit_Framework_TestCase
{
    protected $connectors = array();

    abstract public function provider();

    /**
     * @dataProvider provider
     */
    public function testValidReturns($connector)
    {
        $this->assertTrue(is_array($connector->getTables()));
        $this->assertTrue(is_array($connector->getViews()));
        $this->assertTrue(is_string($connector->getDatabase()));
        $this->assertNull($connector->getOption('nothing_here'));

        $data = $connector->readTableData('nothing');

        if (!is_array($data)) {
            $this->assertInstanceOf('Traversable', $data);
        }
    }
}
