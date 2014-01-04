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

use Indigo\Dumper\Store\StoreInterface;

/**
 * Database ConnectorInterface
 *
 * Handles connection to database and creates dump content
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
interface ConnectorInterface
{
    /**
     * Return database name
     *
     * @return string
     */
    public function getDatabase();

    /**
     * Get list of tables
     *
     * @return array
     */
    public function getTables();

    /**
     * Get list of views
     *
     * @return array
     */
    public function getViews();

    /**
     * Get dump header
     *
     * @return string Dump
     */
    public function dumpHeader();

    /**
     * Get dump footer
     *
     * @return string Dump
     */
    public function dumpFooter();

    /**
     * Dump table schema
     *
     * @param  string $table Table name
     * @return string Table schema
     */
    public function dumpTableSchema($table);

    /**
     * Dump view schema
     *
     * @param  string $view View name
     * @return string View schema
     */
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

    /**
     * Run code before dumping data (locking table, etc)
     *
     * @param  string $table Table name
     * @return string Dump
     */
    public function preDumpTableData($table);

    /**
     * Dump table data
     *
     * @param  string         $table Table name
     * @param  mixed          $data  Data
     * @param  StoreInterface $store Store only passed here to ensure data is written
     */
    public function dumpTableData($table, $data, StoreInterface $store);

    /**
     * Run code after dumping data (unlocking table, etc)
     *
     * @param  string $table Table name
     * @return string Dump
     */
    public function postDumpTableData($table);
}
