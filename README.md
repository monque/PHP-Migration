# PHP Migration

> Readme in [Chinese 中文](https://github.com/monque/PHP-Migration/tree/master/README_ZH.md)

[![Build Status](https://travis-ci.org/monque/PHP-Migration.svg)](https://travis-ci.org/monque/PHP-Migration)

This is a static analyzer for PHP version migration and compatibility checking.

It can suppose your current code running under the new version of PHP then do
checking, and provide advice and treatment.

Features:
- Wide coverage, checks most of the changes which introduced in PHP 5.3, 5.4,
  5.4, 5.6.
- Zero configuration, run directly after download.
- Simply add custom checks.
- Using [PHP-Parser](https://github.com/nikic/PHP-Parser) to parse.

> Compare to the similar project [PHP Compatibility](https://github.com/wimg/PHPCompatibility)
> `PHP Compatibility` is a set of sniffs for `PHP_CodeSniffer`, therefore it
> lacks flexibility and can not checks more changes.
> *just objective comparison*

**Notice: this project is in beta stage, feel free to report any issues.**


## Install / Usage

1. You can download a executable [Phar](http://php.net/manual/en/book.phar.php) file
    ```
    wget http://mo47.com/archive/phpmig.phar
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


## License
This project is released under the [MIT license](http://opensource.org/licenses/MIT).
