# PHP Migration

[![Build Status](https://travis-ci.org/monque/PHP-Migration.svg)](https://travis-ci.org/monque/PHP-Migration)

这是一个用于PHP版本迁移和兼容性检查的代码静态分析器。

主要功能是检查当前代码在新版本PHP下的兼容性并提供相关的建议及处理方法。 
它能够简化升级PHP版本时的步骤，做到精确检查避免遗漏，最终的目标是代替人工检查代码。

有以下特性：
- 检查全面，覆盖PHP 5.3至5.6中绝大部分改动
- 严谨不遗漏，并做到尽可能精准
- 零配置，下载即用
- 可以快速开发适用于个人项目的检查

> 相比于类似项目[PHP Compatibility](https://github.com/wimg/PHPCompatibility)
> 因为`PHP Compatibility`是作为代码规范，并基于`PHP_CodeSniff`开发的
> 导致缺乏一定的灵活度，致使无法覆盖到某些检查
> *此处无意贬低，只是客观对比*

**注意：本项目依然处在早期开发阶段，请谨慎用于生产环境**


## 安装及使用

1. 你可以通过下面命令下载一个封装好的可执行的[Phar](http://php.net/manual/zh/book.phar.php)文件
    ```
    wget http://mo47.com/archive/phpmig.phar
    ```

2. 执行下面命令，将会对该文件进行检查，并输出报告
    ```
    php phpmig.phar sample.php
    ```

    假设下面代码保存在`sample.php`文件中，
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

3. 报告输出内容如下
    表中各列含义如下：行号，问题级别，是否确认，起始版本，详细信息
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
    > 关于第三列`是否确认`的含义，在下面会有详细的解释。

### 选择检查组

一个检查组由多个检查点组成，并且检查组可以指定对其他检查组的依赖。

通过`php phpmig.phar -l`列出全部检查组。

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

并通过`php phpmig.phar -s <setname>`选择要使用的检查组。

> **关于检查组设置的说明**
>
> 举例说明，`to56`是指从任意版本升级至5.6时所用的检查。
> 它依赖`v56`和`to55`，其中`v56`**仅包含**了PHP 5.6引入的改动的检查点，`to55`则依赖`v55`和`to54`，以此类推。
> 通过这种递归依赖的机制，即可实现任意版本到某固定版本的检查。


### 其他用法：输出类的继承关系树

类似于常见的`tree`命令，通过`php phpmig.phar -s classtree .`会扫描项目中的类继承关系，并输出一个树状的图

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

### 通过源代码安装

1. 将本项目clone到本地，并进入项目目录
    ```
    git clone https://github.com/monque/PHP-Migration.git php-migration
    cd php-migration
    ```

2. 执行下面命令来安装 [Composer](https://getcomposer.org/download/)，并通过Composer安装项目所需的依赖
    ```
    curl -sS https://getcomposer.org/installer | php
    php composer.phar install
    ```

3. 可以通过执行下面命令来运行本程序
    ```
    php bin/phpmig
    ```


## 原理、处理流程

### 处理流程

![flow](http://p9.qhimg.com/t010392c0d7e3e01882.png)

### 输出中第三列`是否确认`的含义说明

坦诚的讲，有些存在隐患的改动是永远无法精确检查的。
比如PHP 5.5中unpack的`'a'`表示的格式发生变化（[官方描述](http://php.net/manual/en/migration55.incompatible.php#migration55.incompatible.pack)），并产生了不向下兼容的影响。

以下面代码为例：
``` php
<?php
unpack($obj->getFormat(), $data); // OMG, What is $obj? How getFormat() works?
unpack('b3', $data); // Works in new version
unpack('a3', $data); // Affected
```

通过对参数值的猜测，能够区分出三种不同级别，并进行不同处理

| 级别 | 标记确定 | 报错 |
| ---- | ---- | ---- |
| 确定产生影响 | 是 | 是 |
| 确定不会影响 | 否 | 否 |
| 可能产生影响 | 否 | 是 |

最终输出如下：
```
--------------------------------------------------------------------------------
Found 2 spot(s), 1 identified
--------------------------------------------------------------------------------
   2 | WARNING    |   | 5.5.0 | Behavior of pack() with "a", "A" in format is changed
   4 | WARNING    | * | 5.5.0 | Behavior of pack() with "a", "A" in format is changed
--------------------------------------------------------------------------------
```

这样做即保证了不遗漏，也大幅减少了人工干预。


## 许可

本项目遵循[MIT许可](http://opensource.org/licenses/MIT)进行发布
