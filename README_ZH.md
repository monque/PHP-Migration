# PHP Migration

这是一个用于PHP版本迁移和兼容性检查的静态分析器。

主要功能是检查当前代码在新版本PHP下的兼容性并提供相关的建议及处理方法。

它有以下特性：
- 覆盖面广，可以检查PHP 5.3, 5.4, 5.5, 5.6中绝大部分改动
- 零配置，下载后即可使用
- 可以快速开发适用于个人项目的检查
- 使用[PHP-Parser](https://github.com/nikic/PHP-Parser)进行语法解析

> 相比于类似项目[PHP Compatibility](https://github.com/wimg/PHPCompatibility)
> 因为`PHP Compatibility`是作为代码标准，并基于`PHP_CodeSniff`开发的
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
    array_shift(array(1, 2));  // should pass a variable

    parse_str($url, &$data);  // call-time pass-by-reference is forbidden

    define('__DIR__', dirname(__FILE__));  // __DIR__ is pre-defined
    ```

3. 报告输出内容如下
    表中各列含义如下：行号，问题级别，是否确认，起始版本，详细信息
    ```
    File: sample.php
    --------------------------------------------------------------------------------
        3 | FATAL      | * | 5.3.0 | Only variables can be passed by reference
        5 | FATAL      | * | 5.4.0 | Call-time pass-by-reference has been removed
        7 | WARNING    | * | 5.3.0 | Constant __DIR__ already defined
    --------------------------------------------------------------------------------
    ```

### 其他用法：输出类的继承关系树

类似于常见的`tree`命令，下面命令会扫描项目中的类继承关系，并输出一个树状的图

```
php phpmig.phar -s classtree .
```

输出
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


## 许可

本项目遵循[MIT许可](http://opensource.org/licenses/MIT)进行发布
