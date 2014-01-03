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
 * Variable Store Test
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class VariableStoreTest extends StoreTest
{
    public function setUp()
    {
        $this->store = new VariableStore;
    }
}
