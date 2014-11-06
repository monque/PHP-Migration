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

class Removed extends Change
{
    protected static $version = '5.4.0';

    protected static $prepared = false;

    public static $funcTable = array(
        'define_syslog_variables',
        'import_request_variables',
        'session_is_registered',
        'session_register',
        'session_unregister',
        'mysqli_bind_param',
        'mysqli_bind_result',
        'mysqli_client_encoding',
        'mysqli_fetch',
        'mysqli_param_count',
        'mysqli_get_metadata',
        'mysqli_send_long_data',
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
        if ($this->isRemovedFunc($node)) {
            /*
             * {Errmsg}
             * Fatal error: Call to undefined function {function}
             *
             * {Reference}
             * http://php.net/manual/en/migration54.incompatible.php
             */
            $this->addSpot('FATAL', sprintf('Function %s() is removed', $node->name));
        }
    }

    public function isRemovedFunc($node)
    {
        return ($node instanceof Expr\FuncCall && static::$funcTable->has($node->name));
    }
}
