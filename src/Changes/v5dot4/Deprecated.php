<?php
namespace PhpMigration\Changes\v5dot4;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;

class Deprecated extends Change
{
    protected static $version = '5.4.0';

    protected static $prepared = false;

    public static $funcTable = array(
        'mcrypt_generic_end',
        'mysql_list_dbs',
    );

    public function prepare()
    {
        if (!static::$prepared) {
            static::$funcTable = new SymbolTable(array_flip(static::$funcTable), SymbolTable::IC);
            static::$prepared = true;
        }
    }

    public function leaveNode($node)
    {
        // Function call
        if ($this->isDeprecatedFunc($node)) {
            /**
             * {Errmsg}
             * Deprecated: Function {function} is deprecated
             *
             * {Reference}
             * http://php.net/manual/en/migration54.deprecated.php
             */
            $this->addSpot('FATAL', sprintf('Function %s() is deprecated', $node->name));
        }
    }

    public function isDeprecatedFunc($node)
    {
        return ($node instanceof Expr\FuncCall && static::$funcTable->has($node->name));
    }
}
