<?php
/*
 * This file is part of the Indigo Dump package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dumper\Connector;

use Indigo\Dumper\Store\StoreInterface;

interface ConnectorInterface
{
    public function getHeader();

    public function getFooter();

    public function getTables();

    public function getViews();

    public function dumpTableSchema($table);

    public function dumpViewSchema($view);

    /**
     * Read all data from table if any
     *
     * This function should check for data and return false if empty
     *
     * @param  string $table
     * @return mixed Table data
     */
    public function readTableData($table);
    public function preDumpTableData($table);
    public function dumpTableData($table, $data, StoreInterface $store);
    public function postDumpTableData($table);
}
