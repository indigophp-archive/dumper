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
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;
use PDO;

abstract class AbstractConnector implements ConnectorInterface
{
    const MAX_LINE_SIZE = 1000000;

    protected $options = array();
    protected $pdo;

    protected function resolveOptions(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);
        return $this->options = $resolver->resolve($options);
    }

    /**
     * Set default MySQL dump settings
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

    public function readTableData($table)
    {
        $data = $this->pdo->query("SELECT * FROM `$table`", PDO::FETCH_NUM);

        return $data->rowCount() > 0 ? $data : false;
    }

    public function dumpTableData($table, $data, StoreInterface $store)
    {
        $size = 0;
        $new = true;

        foreach ($this->readTableData($table) as $row) {
            $values = $this->fetchValues($row);
            $values = implode(',', $values);

            $new = $new or ! $this->options['extended_insert'];

            if ($new) {
                $size += $store->write("INSERT INTO `$table` VALUES (" . $values . ")");
                $new = false;
            } else {
                $size += $store->write(",(" . $values . ")");
            }

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

    public function postDumpTableData($table)
    {
        if ($this->options['use_transaction']) {
            $this->commitTransaction();
        }
    }

    private function fetchValues($row)
    {
        $vals = array();

        foreach ($row as $val) {
            $vals[] = is_null($val) ? "NULL" : $this->pdo->quote($val);
        }

        return $vals;
    }

    abstract protected function startTransaction();
    abstract protected function commitTransaction();
}
