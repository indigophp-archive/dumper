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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use PDO;

/**
 * Abstract Connector
 *
 * Uses PDO to connect to database
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
abstract class AbstractConnector implements ConnectorInterface
{
    /**
     * Max line length
     */
    const MAX_LINE_SIZE = 1000000;

    /**
     * Connector options
     *
     * @var array
     */
    protected $options = array();

    /**
     * PDO object
     *
     * @var PDO
     */
    protected $pdo;

    /**
     * Resolve options
     *
     * @param  array  $options
     * @return array Resolved options
     */
    protected function resolveOptions(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        return $this->options = $resolver->resolve($options);
    }

    /**
     * Set default options
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(array('database'));

        $resolver->setDefaults(array(
            'drop_table'                 => false,
            'drop_view'                  => false,
            'disable_foreign_keys_check' => false,
            'use_transaction'            => false,
            'extended_insert'            => true,
            'pdo_options'                => array(
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            ),
        ));

        $resolver->setAllowedTypes(array(
            'database'                   => 'string',
            'drop_table'                 => 'bool',
            'drop_view'                  => 'bool',
            'disable_foreign_keys_check' => 'bool',
            'use_transaction'            => 'bool',
            'extended_insert'            => 'bool',
            'pdo_options'                => 'array',
        ));
    }

    /**
     * Get option
     *
     * @param  string $option  Option key
     * @param  mixed  $default Default value if key is not found
     * @return mixed Option value
     */
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

    /**
     * {@inheritdoc}
     */
    public function getDatabase()
    {
        return $this->options['database'];
    }

    /**
     * {@inheritdoc}
     */
    public function dumpHeader()
    {
        if ($this->options['disable_foreign_keys_check']) {
            return $this->dumpDisableForeignKeysCheck();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dumpFooter()
    {
        if ($this->options['disable_foreign_keys_check']) {
            return $this->dumpEnableForeignKeysCheck();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTables()
    {
        return array_map(function ($item) {
            return reset($item);
        }, $this->showObjects());
    }

    /**
     * {@inheritdoc}
     */
    public function getViews()
    {
        return array_map(function ($item) {
            return reset($item);
        }, $this->showObjects(true));
    }

    /**
     * {@inheritdoc}
     */
    public function dumpTableSchema($table)
    {
        $dump = "-- --------------------------------------------------------" .
            "\n\n" .
            "--\n" .
            "-- Table structure for table `$table`\n" .
            "--\n\n";

        if ($this->options['drop_table']) {
            $dump .= "DROP TABLE IF EXISTS `$table`;\n\n";
        }

        return $dump;
    }

    /**
     * {@inheritdoc}
     */
    public function dumpViewSchema($view)
    {
        $dump = "-- --------------------------------------------------------" .
            "\n\n" .
            "--\n" .
            "-- Table structure for view `$view`\n" .
            "--\n\n";

        if ($this->options['drop_view']) {
            $dump .= "DROP VIEW IF EXISTS `$view`;\n\n";
        }

        return $dump;
    }

    /**
     * {@inheritdoc}
     */
    public function preDumpTableData($table)
    {
        $dump = "--\n" .
            "-- Dumping data for table `$table`\n" .
            "--\n\n";

        if ($this->options['use_transaction']) {
            $this->startTransaction();
        }

        return $dump;
    }

    /**
     * {@inheritdoc}
     */
    public function readTableData($table)
    {
        $data = $this->pdo->query("SELECT * FROM `$table`", PDO::FETCH_NUM);

        return $data->rowCount() > 0 ? $data : false;
    }

    /**
     * {@inheritdoc}
     */
    public function dumpTableData($table, $data, StoreInterface $store)
    {
        $size = 0;
        $new = true;

        foreach ($this->readTableData($table) as $row) {
            $values = $this->fetchValues($row);
            $values = implode(',', $values);

            // Use INSERT statement if line size limit exceeded or extended_insert is disabled
            $new = $new or ! $this->options['extended_insert'];

            if ($new) {
                $size += $store->write("INSERT INTO `$table` VALUES (" . $values . ")");
                $new = false;
            } else {
                $size += $store->write(",(" . $values . ")");
            }

            // Line size limit exceeded
            if ($size > self::MAX_LINE_SIZE or ! $this->options['extended_insert']) {
                $new = true;
                $size = 0;
                $store->write(";\n");
            }
        }

        if (!$new) {
            $store->write(";\n");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postDumpTableData($table)
    {
        if ($this->options['use_transaction']) {
            $this->commitTransaction();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function fetchValues($row)
    {
        $vals = array();

        foreach ($row as $val) {
            $vals[] = is_null($val) ? "NULL" : $this->pdo->quote($val);
        }

        return $vals;
    }

    /**
     * Show tables or views
     *
     * @param  boolean $view Show views
     * @return array
     */
    abstract protected function showObjects($view = false);

    /**
     * Dump disable foreign keys check
     *
     * @return string Dump
     */
    abstract protected function dumpDisableForeignKeysCheck();

    /**
     * Dump enable foreign keys check
     *
     * @return string Dump
     */
    abstract protected function dumpEnableForeignKeysCheck();

    /**
     * Start transaction
     */
    abstract protected function startTransaction();

    /**
     * Commit transaction
     */
    abstract protected function commitTransaction();
}
