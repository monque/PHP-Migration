<?php
namespace PhpMigration\Changes\v5dot3;

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
    protected static $prepared = false;

    public static $funcTable = array(
        'call_user_method'          => 'use call_user_func() instead',
        'call_user_method_array'    => 'use call_user_func_array() instead',
        'define_syslog_variables'   => '',
        // dl() advice copy from http://php.net/manual/en/migration53.sapi.php
        'dl'                        => 'available only under CLI, CGI, and embed SAPIs',
        'ereg'                      => 'use preg_match() instead',
        'ereg_replace'              => 'use preg_replace() instead',
        'eregi'                     => 'use preg_match() with the "i" modifier instead',
        'eregi_replace'             => 'use preg_replace() with the "i" modifier instead',
        'set_magic_quotes_runtime'  => '',
        'magic_quotes_runtime'      => '',
        'session_register'          => 'use the $_SESSION superglobal instead',
        'session_unregister'        => 'use the $_SESSION superglobal instead',
        'session_is_registered'     => 'use the $_SESSION superglobal instead',
        'set_socket_blocking'       => 'use stream_set_blocking() instead',
        'split'                     => 'use preg_split() instead',
        'spliti'                    => 'use preg_split() with the "i" modifier instead',
        'sql_regcase'               => '',
        'mysql_db_query'            => 'use mysql_select_db() and mysql_query() instead',
        'mysql_escape_string'       => 'use mysql_real_escape_string() instead',
        // Passing locale category names as strings is now deprecated. Use the LC_* family of constants instead.
        // The is_dst parameter to mktime(). Use the new timezone handling functions instead.
    );

    public function prepare()
    {
        if (!static::$prepared) {
            static::$funcTable = new SymbolTable(static::$funcTable, SymbolTable::IC);
            static::$prepared = true;
        }
    }

    public function leaveNode($node)
    {
        // Function call
        if ($this->isDeprecatedFunc($node)) {
            $advice = static::$funcTable->get($node->name);
            if ($advice) {
                $errmsg = sprintf('Function %s() is deprecated, %s', $node->name, $advice);
            } else {
                $errmsg = sprintf('Function %s() is deprecated', $node->name);
            }
            $this->visitor->addSpot($errmsg);

        // Assign new instance
        } elseif ($this->isAssignNewByRef($node)) {
            $this->visitor->addSpot('Assigning the return value of new by reference is deprecated');

        // Call-time pass-by-reference
        } elseif ($this->isCallTimePassByRef($node)) {
            /*
             * {Description}
             * Call-time pass-by-reference is now deprecated
             *
             * {Reference}
             * http://php.net/manual/en/migration53.deprecated.php
             */
            $this->visitor->addSpot('Calltime pass-by-reference is deprecated');
        }
    }

    public function isDeprecatedFunc($node)
    {
        return ($node instanceof Expr\FuncCall && static::$funcTable->has($node->name));
    }

    public function isAssignNewByRef($node)
    {
        return ($node instanceof Expr\AssignRef && $node->expr instanceof Expr\New_);
    }

    public function isCallTimePassByRef($node)
    {
        if (!($node instanceof Expr\FuncCall || $node instanceof Expr\StaticCall ||
                $node instanceof Expr\MethodCall)) {
            return false;
        }

        foreach ($node->args as $arg) {
            if ($arg->byRef) {
                return true;
            }
        }
        return false;
    }
}
