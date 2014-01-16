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

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local as Adapter;

/**
 * Flysystem Store Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class FlysystemStoreTest extends StoreTest
{
    public function setUp()
    {

        $this->store = new FlysystemStore(new Filesystem(new Adapter('/tmp')), 'test.file');
    }
}
