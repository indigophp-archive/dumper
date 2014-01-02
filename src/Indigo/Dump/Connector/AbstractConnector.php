<?php
/*
 * This file is part of the Indigo Dump package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dump\Connector;

abstract class AbstractConnector implements ConnectorInterface
{
    protected $options = array();

    public function getOption($option = null, $default = null)
    {
        if (is_null($option)) {
            return $this->options;
        } elseif (array_key_exists($option, $this->options)) {
            return $this->options[$option];
        } else {
            return $default;
        }
    }
}
