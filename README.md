# PHP Migration

> Readme in [Chinese 中文](https://github.com/monque/PHP-Migration/tree/master/README_ZH.md)

[![Build Status](https://travis-ci.org/monque/PHP-Migration.svg)](https://travis-ci.org/monque/PHP-Migration)

This is a static analyzer for PHP version migration and compatibility checking.

It can suppose your current code running under the new version of PHP then do
checking, and provide advice and treatment.

And it can simplify the process of upgrading PHP. Its goal is instead of manual
checking.

Features:
- Wide coverage, checks most of the changes which introduced in PHP 5.3 - 5.6.
- Strict, without missing any risk.
- Zero configuration, run directly after download.
- Simply add custom checks.

> Compare to the similar project [PHP Compatibility](https://github.com/wimg/PHPCompatibility)
> `PHP Compatibility` is a set of sniffs for `PHP_CodeSniffer`, therefore it
> lacks flexibility and can not checks more changes.
> *just objective comparison*

**Notice: this project is in beta stage, feel free to report any issues.**


## Install / Usage

1. You can download a executable [Phar](http://php.net/manual/en/book.phar.php) file
    ```
    wget https://github.com/monque/PHP-Migration/releases/download/v0.1.2/phpmig.phar
    ```

2. Use the following command to check PHP file
    ```
    php phpmig.phar sample.php
    ```

    Suppose these code stored in `sample.php`
    ``` php
    <?php
    // Fatal error: Only variables can be passed by reference
    array_shift(array(1, 2));
    sort(array(1, 2, 3));

    // __DIR__ is pre-defined
    define('__DIR__', dirname(__FILE__));

    // Fatal error: Cannot redeclare class_alias()
    function class_alias() {}
    if (!function_exists('class_alias')) {
        function class_alias() {}
    }

    // Fatal error: Call-time pass-by-reference has been removed
    array_map('trim', &$_SERVER);

    // Fatal error: 'break' operator with non-constant operand is no longer supported
    while (true) {
        break $a;
    }

    // Fatal error: Cannot re-assign auto-global variable _GET
    function ohno($_GET) {}

    // Fatal error:  Call to undefined function php_logo_guid()
    php_logo_guid();
    ```

3. Output report
    Each columns means: Line Number, Level, Identified, Version, Message
    ```
    File: sample.php
    --------------------------------------------------------------------------------
    Found 8 spot(s), 8 identified
    --------------------------------------------------------------------------------
        3 | FATAL      | * | 5.3.0 | Only variables can be passed by reference
        4 | FATAL      | * | 5.3.0 | Only variables can be passed by reference
        7 | WARNING    | * | 5.3.0 | Constant __DIR__ already defined
       10 | FATAL      | * | 5.3.0 | Cannot redeclare class_alias()
       16 | FATAL      | * | 5.4.0 | Call-time pass-by-reference has been removed
       20 | FATAL      | * | 5.4.0 | break operator with non-constant operand is no longer supported
       24 | FATAL      | * | 5.4.0 | Cannot re-assign auto-global variable
       27 | FATAL      | * | 5.5.0 | Function php_logo_guid() is removed
    --------------------------------------------------------------------------------
    ```
    > The third field `Identified` will be explained at bottom.


### Set Selection

A Checking Set contains muiltiple Check Class, and the dependence of Set can be
specified.

List all sets through command `php phpmig.phar -l`.

```
classtree  => List contents of classes in a tree-like format
to53       => Migrating from ANY version to PHP 5.3.x
to54       => Migrating from ANY version to PHP 5.4.x
to55       => Migrating from ANY version to PHP 5.5.x
to56       => Migrating from ANY version to PHP 5.6.x
v53        => Migrating from PHP 5.2.x to PHP 5.3.x
v54        => Migrating from PHP 5.3.x to PHP 5.4.x
v55        => Migrating from PHP 5.4.x to PHP 5.5.x
v56        => Migrating from PHP 5.5.x to PHP 5.6.x
```

And add param `-s` like `php phpmig.phar -s <setname>` to select a set to use.


### Utility, print class tree

Like the common linux command `tree`, the following command will scan files and
output all classes in a tree-like format.
```
php phpmig.phar -s classtree .
```

Output:
```
|-- PhpMigration\App
|-- PhpMigration\Changes\AbstractChange
|   |-- PhpMigration\Changes\AbstractIntroduced
|   |   |-- PhpMigration\Changes\v5dot3\Introduced
|   |   |-- PhpMigration\Changes\v5dot4\Introduced
|   |   |-- PhpMigration\Changes\v5dot5\Introduced
|   |   `-- PhpMigration\Changes\v5dot6\Introduced
|   |-- PhpMigration\Changes\AbstractKeywordReserved
|   |   |-- PhpMigration\Changes\v5dot3\IncompReserved
|   |   `-- PhpMigration\Changes\v5dot4\IncompReserved
|   |-- PhpMigration\Changes\AbstractRemoved
|   |   |-- PhpMigration\Changes\v5dot3\Removed
|   |   |-- PhpMigration\Changes\v5dot4\Removed
|   |   |-- PhpMigration\Changes\v5dot5\Removed
|   |   `-- PhpMigration\Changes\v5dot6\Removed
|   |-- PhpMigration\Changes\ClassTree
|   |-- PhpMigration\Changes\v5dot3\Deprecated
|   |-- PhpMigration\Changes\v5dot3\IncompByReference
|   |-- PhpMigration\Changes\v5dot3\IncompCallFromGlobal
|   |-- PhpMigration\Changes\v5dot3\IncompMagic
|   |-- PhpMigration\Changes\v5dot3\IncompMagicInvoked
|   |-- PhpMigration\Changes\v5dot3\IncompMisc
|   |-- PhpMigration\Changes\v5dot4\Deprecated
|   |-- PhpMigration\Changes\v5dot4\IncompBreakContinue
|   |-- PhpMigration\Changes\v5dot4\IncompByReference
|   |-- PhpMigration\Changes\v5dot4\IncompMisc
|   |-- PhpMigration\Changes\v5dot4\IncompParamName
|   |-- PhpMigration\Changes\v5dot4\IncompRegister
|   |-- PhpMigration\Changes\v5dot5\Deprecated
|   |-- PhpMigration\Changes\v5dot5\IncompCaseInsensitive
|   |-- PhpMigration\Changes\v5dot5\IncompPack
|   |-- PhpMigration\Changes\v5dot6\Deprecated
|   |-- PhpMigration\Changes\v5dot6\IncompMisc
|   `-- PhpMigration\Changes\v5dot6\IncompPropertyArray
|-- PhpMigration\CheckVisitor
|-- PhpMigration\Logger
|-- PhpMigration\SymbolTable
|-- PhpMigration\Utils\FunctionListExporter
|-- PhpMigration\Utils\Logging
|-- PhpMigration\Utils\Packager
`-- PhpMigration\Utils\ParserHelper
```

### Manual Installation from Source

1. Clone this project to your local path
    ```
    git clone https://github.com/monque/PHP-Migration.git php-migration
    cd php-migration
    ```

2. Using [Composer](https://getcomposer.org/download/) to install dependencies
    ```
    curl -sS https://getcomposer.org/installer | php
    php composer.phar install
    ```

3. Verify it works
    ```
    php bin/phpmig
    ```


## Explaination

### Process Flow

![flow](http://p9.qhimg.com/t010392c0d7e3e01882.png)

### About the third field `Identified` in outputing

To be honest, not all code will be checked accurately as you expect.

Some changes will never be checked accurately, and it's has nothing to do with
someone's ability or technology.

For example, [`unpack()` changes in PHP
5.5](http://php.net/manual/en/migration55.incompatible.php#migration55.incompatible.pack),
it now keeps trailing NULL bytes when the "a" format code is used.

Code below:
``` php
<?php
unpack($obj->getFormat(), $data); // OMG, What is $obj? How getFormat() works?
unpack('b3', $data); // Works in new version
unpack('a3', $data); // Affected
```

But we can guess the value of variables, and make a level table:

| Level | Identified | Output |
| ---- | ---- | ---- |
| MUST affect | Yes | Yes |
| MUST NOT affect | Yes | No |
| MAY  affect | No | Yes |

So, finally output
```
--------------------------------------------------------------------------------
Found 2 spot(s), 1 identified
--------------------------------------------------------------------------------
   2 | WARNING    |   | 5.5.0 | Behavior of pack() with "a", "A" in format is changed
   4 | WARNING    | * | 5.5.0 | Behavior of pack() with "a", "A" in format is changed
--------------------------------------------------------------------------------
```


## License
This project is released under the [MIT license](http://opensource.org/licenses/MIT).
