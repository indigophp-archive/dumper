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
    protected $connector;

    public function testValidReturns()
    {
        $this->assertTrue(is_array($this->connector->getTables()));
        $this->assertTrue(is_array($this->connector->getViews()));
        $this->assertTrue(is_string($this->connector->getDatabase()));
        $this->assertNull($this->connector->getOption('nothing_here'));

        $data = $this->connector->readTableData('nothing');

        if (!is_array($data)) {
            $this->assertInstanceOf('Traversable', $data);
        }
    }
}
