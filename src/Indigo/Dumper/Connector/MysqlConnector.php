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

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use PDO;

class MysqlConnector extends AbstractConnector
{
    public function __construct(array $options)
    {
        $options = $this->resolveOptions($options);

        $dsn = 'mysql:';

        if (empty($options['unix_socket'])) {
            $dsn .= 'host=' . $options['host'] . ';port=' . $options['port'];
        } else {
            $dsn .= 'unix_socket=' . $options['unix_socket'];
        }

        $dsn .= ';dbname=' . $options['database'];

        $this->pdo = new PDO(
            $dsn,
            $options['username'],
            $options['password'],
            $options['pdo_options']
        );

        // This is needed on some PHP versions
        $this->pdo->exec("SET NAMES utf8");
    }

    /**
     * Set default MySQL connection and dump details
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'host'          => 'localhost',
            'port'          => 3306,
            'drop_database' => false,
            'lock_tables'   => false,
            'add_lock'      => true,
            'pdo_options'   => array(
                PDO::ATTR_PERSISTENT         => true,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            ),
        ));

        $resolver->setOptional(array('unix_socket'));

        $resolver->setRequired(array('username', 'password'));

        $resolver->setAllowedTypes(array(
            'host'          => 'string',
            'port'          => 'integer',
            'unix_socket'   => 'string',
            'username'      => 'string',
            'password'      => 'string',
            'drop_database' => 'bool',
            'lock_tables'   => 'bool',
            'add_lock'      => 'bool',
        ));
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
        $query = $this->pdo->prepare('SHOW FULL TABLES WHERE `Table_type` LIKE :type');
        $query->execute(array(':type' => $view ? 'VIEW' : 'BASE TABLE'));

        return $query->fetchAll();
    }

    protected function dumpDisableForeignKeysCheck()
    {
        return "-- Ignore checking of foreign keys\n" .
            "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    }

    protected function dumpEnableForeignKeysCheck()
    {
        return "\n-- Unignore checking of foreign keys\n" .
            "SET FOREIGN_KEY_CHECKS = 1;\n\n";
    }

    protected function dumpAddDropDatabase()
    {
        $charset = $this->pdo->query("SHOW VARIABLES LIKE 'character_set_database';")->fetchColumn(1);
        $collation = $this->pdo->query("SHOW VARIABLES LIKE 'collation_database';")->fetchColumn(1);

        return "/*!40000 DROP DATABASE IF EXISTS `" . $this->options['database'] . "`*/;\n".
            "CREATE DATABASE /*!32312 IF NOT EXISTS*/ `" . $this->options['database'] .
            "` /*!40100 DEFAULT CHARACTER SET " . $charset .
            " COLLATE " . $collation . "*/;\n" .
            "USE `" . $this->options['database'] . "`;\n\n";
    }

    public function dumpCreateTable($table)
    {
        $dump = parent::dumpCreateTable($table);

        $dump .= $this->pdo->query("SHOW CREATE TABLE `$table`")->fetchColumn(1) . ";\n\n";

        return $dump;
    }

    public function dumpCreateView($view)
    {
        $dump = parent::dumpCreateView($view);

        $dump .= $this->pdo->query("SHOW CREATE VIEW `$view`")->fetchColumn(1) . ";\n\n";

        return $dump;
    }

    protected function startTransaction()
    {
        $this->pdo->exec("SET GLOBAL TRANSACTION ISOLATION LEVEL REPEATABLE READ; START TRANSACTION");
    }

    protected function commitTransaction()
    {
        $this->pdo->exec('COMMIT');
    }

    public function preTableData($table)
    {
        if ($this->options['lock_tables']) {
            $this->pdo->exec("LOCK TABLES `$table` READ LOCAL");
        }

        if ($this->options['add_lock']) {
            return "LOCK TABLES `$table` WRITE;\n";
        }
    }

    public function postTableData($table)
    {
        if ($this->options['lock_tables']) {
            $this->pdo->exec('UNLOCK TABLES');
        }

        if ($this->options['add_lock']) {
            return "UNLOCK TABLES;\n";
        }
    }
}
