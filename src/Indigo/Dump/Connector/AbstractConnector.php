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

    public function dumpCreateTable($table)
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

    public function dumpCreateView($view)
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

    public function dumpTableData($table)
    {
        $dump = "--\n" .
            "-- Dumping data for table `$table`\n" .
            "--\n\n";

        if ($this->options['use_transaction']) {
            $this->startTransaction();
        }

        $dump .= $this->preTableData($table);

        $size = 0;
        $new = true;

        foreach ($this->pdo->query("SELECT * FROM `$table`", PDO::FETCH_NUM) as $row) {
            $values = $this->fetchValues($row);
            $values = implode(',', $values);
            $size += strlen($values);

            $new = $new or ! $this->options['extended_insert'];

            if ($new) {
                $dump .= "INSERT INTO `$table` VALUES (" . $values . ")";
                $new = false;
            } else {
                $dump .= ",(" . $values . ")";
            }

            if ($size > self::MAX_LINE_SIZE or ! $this->options['extended_insert']) {
                $new = true;
                $dump .= ";\n";
            }
        }

        if (!$new) {
            $dump .= ";\n";
        }

        $dump .= $this->postTableData($table);

        if ($this->options['use_transaction']) {
            $this->commitTransaction();
        }

        return $dump;
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
    abstract protected function preTableData($table);
    abstract protected function postTableData($table);
}
