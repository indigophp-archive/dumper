# Indigo Dumper

[![Build Status](https://travis-ci.org/indigophp/dumper.png?branch=develop)](https://travis-ci.org/indigophp/dumper)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/indigophp/dumper/badges/quality-score.png?s=67092d22b834c80f6cd14300c7a57f8d099c6b07)](https://scrutinizer-ci.com/g/indigophp/dumper/)
[![Code Coverage](https://scrutinizer-ci.com/g/indigophp/dumper/badges/coverage.png?s=794fee9a844c2f9ee7db2cd3f6f66864c81e0926)](https://scrutinizer-ci.com/g/indigophp/dumper/)

**Dump databases to SQL file and store it where you want**


Currently supported databases:

* MySQL
* Sqlite


## Install

Via Composer

``` json
{
    "require": {
        "indigophp/dumper": "@stable"
    }
}
```

## Usage

``` php
// Options and default values
$options => array(
    /* Required options */
    'database' => 'test',

    /* Connector settings */
    'drop_table'                 => false, /* Add drop table statement */
    'drop_view'                  => false, /* Add drop view statement */
    'disable_foreign_keys_check' => false,
    'use_transaction'            => false, /* Use transaction for data retrieving */
    'extended_insert'            => true, /* Use extended insert statements */
    'pdo_options'                => array(), /* Options passed to the PDO driver */

    /* MySQL Connector specific options */
    /* Required options */
    'username' => 'test',
    'password' => 'secret',

    /* Optional options */
    'host'          => 'localhost',
    'port'          => 3306,
    'unix_socket'   => 'unix:///var/run/mysql.sock',
    'drop_database' => false, /* Add drop database statement */
    'use_lock'      => false, /* Lock tables during data retrieve */
    'lock_table'    => true, /* Add lock table statement */
);
$connector = new Indigo\Dumper\Connector\MysqlConnector($options);
$store = new Indigo\Dumper\Store\GzStore();

// Options and default values
$options = array(
    'tables' => true, /* Include tables */
    'no_data' => false, /* If true, data will be skipped */
    'views' => true, /* Include views */
);

$dumper = new Indigo\Dumper\Dumper($connector, $store, $options);

// returns file path
$dumper->dump('/path/to/file.sql.gz');
```

## Advanced usage

Without setting any table/view on dumper, all of them will be dumped. Here is how you can control which table/view should be dumped:

``` php
// returns false
$dumper->hasTable();

$dumper->includeTable('test')->includeTable(array('test2', 'test3'));
$dumper->excludeTable('test2');

// returns true
$dumper->hasTable();

// returns true
$dumper->isTableIncluded('test');

// returns false
$dumper->isTableExcluded('test');


$dumper->includeView('v_test')->includeView(array('v_test2', 'v_test3'));
$dumper->excludeView('v_test2');
```

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/indigophp/dumper/blob/develop/CONTRIBUTING.md) for details.

## Credits

- [Márk Sági-Kazár](https://github.com/sagikazarmark)
- [All Contributors](https://github.com/indigophp/dumper/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/indigophp/dumper/blob/develop/LICENSE) for more information.