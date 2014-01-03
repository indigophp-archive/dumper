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

use PDO;

/**
 * Sqlite Connector
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class SqliteConnector extends AbstractConnector
{
    public function __construct(array $options)
    {
        $options = $this->resolveOptions($options);

        $this->pdo = new PDO(
            'sqlite:' . $options['database'],
            null,
            null,
            $options['pdo_options']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function showObjects($view = false)
    {
        $type = $view ? 'view' : 'table';
        return $this->pdo->query("SELECT tbl_name FROM sqlite_master WHERE type='$type'")->fetchAll();
    }

    /**
     * {@inheritdoc}
     */
    protected function dumpDisableForeignKeysCheck()
    {
        return "-- Ignore checking of foreign keys\n" .
            "PRAGMA foreign_keys=OFF;\n\n";
    }

    /**
     * {@inheritdoc}
     */
    protected function dumpEnableForeignKeysCheck()
    {
        return "\n-- Unignore checking of foreign keys\n" .
            "PRAGMA foreign_keys=ON;\n\n";
    }

    /**
     * {@inheritdoc}
     */
    public function dumpTableSchema($table)
    {
        $dump = parent::dumpTableSchema($table);

        $dump .= $this->pdo->query(
            "SELECT `sql` FROM sqlite_master WHERE `type` = 'table' AND `tbl_name` = '$table'"
        )->fetchColumn(0) . ";\n\n";

        $dump .= $this->dumpTableIndexes($table);

        return $dump;
    }

    /**
     * Dump table indexes
     *
     * @param  string $table Table name
     * @return string Dump
     */
    protected function dumpTableIndexes($table)
    {
        $dump = '';
        $indexes = $this->pdo->query(
            "SELECT `sql` FROM sqlite_master WHERE `type` = 'index' AND `tbl_name` = '$table'"
        );

        foreach ($indexes as $index) {
            $dump .= reset($index) . ";\n\n";
        }

        return $dump;
    }

    /**
     * {@inheritdoc}
     */
    public function dumpViewSchema($view)
    {
        $dump = parent::dumpViewSchema($view);

        $dump .= $this->pdo->query(
            "SELECT `sql` FROM sqlite_master WHERE `type` = 'view' AND `tbl_name` = '$view'"
        )->fetchColumn(0) . ";\n\n";

        return $dump;
    }

    /**
     * {@inheritdoc}
     */
    public function readTableData($table)
    {
        $count = $this->pdo->query("SELECT COUNT(*) FROM `$table`", PDO::FETCH_NUM)->fetchColumn(0);
        $data = $this->pdo->query("SELECT * FROM `$table`", PDO::FETCH_NUM);

        return $count > 0 ? $data : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function startTransaction()
    {
        $this->pdo->exec('BEGIN EXCLUSIVE');
    }

    /**
     * {@inheritdoc}
     */
    protected function commitTransaction()
    {
        $this->pdo->exec('COMMIT');
    }
}
