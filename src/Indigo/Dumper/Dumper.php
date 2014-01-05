<?php
/*
 * This file is part of the Indigo Dumper package.
 *
 * (c) IndigoPHP Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Dumper;

use Indigo\Dumper\Connector\ConnectorInterface;
use Indigo\Dumper\Store\StoreInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\OptionsResolver\Options;

/**
 * Dumper
 *
 * Executes dump functions, collects data and sends to the store
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class Dumper
{
    /**
     * Connector object
     *
     * @var ConnectorInterface
     */
    protected $connector;

    /**
     * Store object
     *
     * @var StoreInterface
     */
    protected $store;

    /**
     * Options
     *
     * @var array
     */
    protected $options = array();

    /**
     * Included/excluded tables
     *
     * @var array
     */
    protected $tables = array();

    /**
     * Included/excluded views
     *
     * @var array
     */
    protected $views = array();

    /**
     * Dumper constructor
     *
     * @param ConnectorInterface $connector
     * @param StoreInterface     $store
     * @param array              $options
     */
    public function __construct(
        ConnectorInterface $connector,
        StoreInterface $store,
        array $options = array()
    ) {
        $this->connector = $connector;
        $this->store = $store;

        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver, true);
        $this->options = $resolver->resolve($options);
    }

    /**
     * Set default Dumper options
     *
     * @param OptionsResolverInterface $resolver
     */
    protected function setDefaultOptions(OptionsResolverInterface $resolver, $global = false)
    {
        $resolver->setDefaults(array(
            'tables'  => true,
            'no_data' => false,
            'views'   => true,
        ));
    }

    /**
     * Get option
     *
     * @param  string $option  Option key
     * @param  mixed  $default Default value if key is not found
     * @return mixed  Option value
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
     * Get database name
     *
     * @return string
     */
    public function getDatabase()
    {
        return $this->connector->getDatabase();
    }

    /**
     * Get Connector object
     *
     * @return ConnectorInterface
     */
    public function getConnector()
    {
        return $this->connector;
    }

    /**
     * Get Store object
     *
     * @return StoreInterface
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Set store to a new instance
     *
     * @param StoreInterface $store
     */
    public function setStore(StoreInterface $store)
    {
        $this->store = $store;

        return $this;
    }

    /**
     * Add included table
     *
     * @param  string $table Table name
     * @return Dumper
     */
    public function includeTable($table)
    {
        $this->setObject($table, $this->tables);

        return $this;
    }

    /**
     * Add excluded table
     *
     * @param  string $table Table name
     * @return Dumper
     */
    public function excludeTable($table)
    {
        $this->setObject($table, $this->tables, false);

        return $this;
    }

    /**
     * Are there any table included?
     *
     * @return boolean
     */
    public function hasTable()
    {
        return $this->hasObject($this->tables);
    }

    /**
     * Is table included?
     *
     * @param  string  $table
     * @return boolean
     */
    public function isTableIncluded($table)
    {
        return $this->isObject($table, $this->tables, true);
    }

    /**
     * Is table excluded?
     *
     * @param  string  $table
     * @return boolean
     */
    public function isTableExcluded($table)
    {
        return $this->isObject($table, $this->tables, false);
    }

    /**
     * Add included view
     *
     * @param  string $view View name
     * @return Dumper
     */
    public function includeView($view)
    {
        $this->setObject($view, $this->views);

        return $this;
    }

    /**
     * Add excluded view
     *
     * @param  string $view View name
     * @return Dumper
     */
    public function excludeView($view)
    {
        $this->setObject($view, $this->views, false);

        return $this;
    }

    /**
     * Are there any view included?
     *
     * @return boolean
     */
    public function hasView()
    {
        return $this->hasObject($this->views);
    }

    /**
     * Is view included?
     *
     * @param  string  $view
     * @return boolean
     */
    public function isViewIncluded($view)
    {
        return $this->isObject($view, $this->views, true);
    }

    /**
     * Is view excluded?
     *
     * @param  string  $view
     * @return boolean
     */
    public function isViewExcluded($view)
    {
        return $this->isObject($view, $this->views, false);
    }

    /**
     * Set object include/exclude
     *
     * @param string  $object  Object name
     * @param array   $objects  Objects
     * @param boolean $value   Include or exclude object
     */
    protected function setObject($object, & $objects, $value = true)
    {
        if (is_array($object)) {
            foreach ($object as $o) {
                $this->setObject($o, $objects, $value);
            }
        } else {
            if (empty($object) or ! is_string($object)) {
                throw new \InvalidArgumentException('Invalid name: "' . $object . '"');
            }

            $objects[$object] = $value;
        }
    }

    /**
     * Is object ...?
     * @param  string  $table
     * @param  boolean $value
     * @return boolean
     */
    protected function isObject($object, array $objects, $value)
    {
        return array_key_exists($object, $objects) and $objects[$object] === $value;
    }

    /**
     * Are there any object included?
     *
     * @return boolean
     */
    protected function hasObject($objects)
    {
        // Excluded object does not count
        $objects = array_filter($objects);

        return ! empty($objects);
    }

    /**
     * Dump database
     *
     * @return boolean Success
     */
    public function dump()
    {
        $this->write($this->connector->dumpHeader());

        if ($this->options['tables']) {
            $this->dumpTables();
        }

        if ($this->options['views']) {
            $this->dumpViews();
        }

        $this->write($this->connector->dumpFooter());

        return $this->store->save();
    }

    /**
     * Dump tables
     */
    protected function dumpTables()
    {
        $tables = $this->connector->getTables();

        if ($this->hasTable()) {
            $tables = array_filter($tables, array($this, 'isTableIncluded'));
        }

        foreach ($tables as $table) {
            $this->write($this->connector->dumpTableSchema($table));

            if ($this->options['no_data'] === false) {
                $this->dumpTableData($table);
            }
        }
    }

    /**
     * Dump table data
     *
     * @param string $table
     */
    protected function dumpTableData($table)
    {
        // Workaround because Sqlite does not support rowCount
        if ($data = $this->connector->readTableData($table)) {
            // Get header, locks, etc
            $this->write($this->connector->preDumpTableData($table));

            // We pass store here, as we don't want to store too much data in a variable
            $this->connector->dumpTableData($table, $data, $this->store);

            // Get footer, unlocks, etc
            $this->write($this->connector->postDumpTableData($table));
        }
    }

    /**
     * Dump views
     */
    protected function dumpViews()
    {
        $views = $this->connector->getViews();

        if ($this->hasView()) {
            $views = array_filter($views, array($this, 'isViewIncluded'));
        }

        foreach ($views as $view) {
            $this->write($this->connector->dumpViewSchema($view));
        }
    }

    /**
     * Write data to store
     *
     * @param  string  $data
     * @return integer Bytes written
     */
    protected function write($data)
    {
        return $this->store->write($data);
    }

    /**
     * Read data from store
     *
     * @return string
     */
    public function read()
    {
        return $this->store->read();
    }
}
