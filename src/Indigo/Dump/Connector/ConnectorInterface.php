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

interface ConnectorInterface
{
    public function getHeader();

	public function getFooter();

	public function getTables();

	public function getViews();

    public function dumpCreateTable($table);

	public function dumpCreateView($view);
}
