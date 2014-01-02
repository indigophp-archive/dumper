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

class MysqlConnector extends AbstractConnector
{
    protected $pdo;
    protected $settings;

    public function __construct(array $options, array $settings = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        $options = $this->options = $resolver->resolve($options);

        $resolver = new OptionsResolver();
        $this->setDefaultSettings($resolver);
        $settings = $this->settings = $resolver->resolve($settings);

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
            $settings['pdo_options']
        );

        // This is needed on some PHP versions
        $this->pdo->exec("SET NAMES utf8");
    }

    /**
     * Set default MySQL connection details
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'host' => 'localhost',
            'port' => 3306,
        ));

        $resolver->setOptional(array('unix_socket'));

        $resolver->setRequired(array('username', 'password', 'database'));

        $resolver->setAllowedTypes(array(
            'host'        => 'string',
            'port'        => 'integer',
            'unix_socket' => 'string',
            'username'    => 'string',
            'password'    => 'string',
            'database'    => 'string',
        ));
    }

    /**
     * Set default MySQL dump settings
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultSettings(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
			'drop_database'              => false,
			'drop_table'                 => false,
			'drop_view'                  => false,
			'single_transaction'         => false,
			'lock_tables'                => false,
			'add_locks'                  => true,
			'extended_insert'            => true,
			'disable_foreign_keys_check' => false,
			'pdo_options'                => array(
                PDO::ATTR_PERSISTENT         => true,
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'
            )
        ));


        $resolver->setAllowedTypes(array(
			'drop_database'              => 'bool',
			'drop_table'                 => 'bool',
			'drop_view'                  => 'bool',
			'single_transaction'         => 'bool',
			'lock_tables'                => 'bool',
			'add_locks'                  => 'bool',
			'extended_insert'            => 'bool',
			'disable_foreign_keys_check' => 'bool',
			'pdo_options'                => 'array'
        ));
    }

    public function getHeader()
    {
        $header = '';

        if ($this->settings['disable_foreign_keys_check']) {
            $header .= $this->dumpDisableForeignKeysCheck();
        }

        if ($this->settings['drop_database']) {
            $header .= $this->dumpAddDropDatabase();
        }

        return $header;
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
        $query = $this->pdo->prepare('SHOW FULL TABLES WHERE Table_type LIKE :type');
        $query->execute(array(':type' => $view ? 'VIEW' : 'BASE TABLE'));

        return $query->fetchAll();
    }

    protected function dumpDisableForeignKeysCheck()
    {
        return "-- Ignore checking of foreign keys\n" .
            "SET FOREIGN_KEY_CHECKS = 0;\n\n";
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
    	$dump = '';

	    if ($this->settings['drop_table']) {
	    	$dump .= "DROP TABLE IF EXISTS `$table`;\n\n";
	    }

	    $dump .= $this->pdo->query('SHOW CREATE TABLE ' . $table)->fetchColumn(1);

	    return $view;
    }

    public function dumpCreateView($view)
    {
    	$dump = '';

	    if ($this->settings['drop_view']) {
	    	$dump .= "DROP VIEW IF EXISTS `$view`;\n\n";
	    }

	    $dump .= $this->pdo->query('SHOW CREATE VIEW ' . $view)->fetchColumn(1);

	    return $dump;
    }
}
