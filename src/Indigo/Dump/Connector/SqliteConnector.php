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

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use PDO;

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

    public function getHeader()
    {
        $header = '';

        if ($this->options['disable_foreign_keys_check']) {
            $header .= $this->dumpDisableForeignKeysCheck();
        }

        if ($this->options['drop_database']) {
            $header .= $this->dumpAddDropDatabase();
        }

        return $header;
    }

    public function getFooter()
    {
        $footer = '';

        if ($this->options['disable_foreign_keys_check']) {
            $footer .= $this->dumpEnableForeignKeysCheck();
        }

        return $footer;
    }

    public function getTables()
    {
        return array_map(function($item) {
            return reset($item);
        }, $this->showTables());
    }

    public function getViews()
    {
        return array_map(function($item) {
            return reset($item);
        }, $this->showTables(true));
    }

    private function showTables($view = false)
    {
        $query = $this->pdo->prepare("SELECT tbl_name FROM sqlite_master WHERE type=':type'");
        $query->execute(array(':type' => $view ? 'view' : 'table'));

        return $query->fetchAll();
    }

    protected function dumpDisableForeignKeysCheck()
    {
        return "-- Ignore checking of foreign keys\n" .
            "PRAGMA foreign_keys=OFF;\n\n";
    }

    protected function dumpEnableForeignKeysCheck()
    {
        return "\n-- Unignore checking of foreign keys\n" .
            "PRAGMA foreign_keys=ON;\n\n";
    }

    public function dumpCreateTable($table)
    {
        $dump = parent::dumpCreateTable($table);

        $dump .= $this->pdo->query("SELECT `sql` FROM sqlite_master WHERE `type` = 'table' AND `table` = '$table'")->fetchColumn(0) . ";\n\n";

        return $dump;
    }

    public function dumpCreateView($view)
    {
        $dump = parent::dumpCreateView($view);

        $dump .= $this->pdo->query("SELECT `sql` FROM sqlite_master WHERE `type` = 'view' AND `table` = '$view'")->fetchColumn(0) . ";\n\n";

        return $dump;
    }

    protected function startTransaction()
    {
        $this->pdo->exec('BEGIN EXCLUSIVE');
    }

    protected function commitTransaction()
    {
        $this->pdo->exec('COMMIT');
    }

    protected function preTableData($table)
    {
    }

    protected function postTableData($table)
    {
    }
}
