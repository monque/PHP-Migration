<?php
namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpMigration\Changes\RemoveTableItemTrait;
use PhpParser\Node\Expr;

class Deprecated extends AbstractChange
{
    use RemoveTableItemTrait;

    protected static $version = '5.3.0';

    protected $funcTable = [
        'call_user_method'          => 'use call_user_func() instead',
        'call_user_method_array'    => 'use call_user_func_array() instead',
        'define_syslog_variables'   => '',
        // 'dl'                        => 'available only under CLI, CGI, and embed SAPIs', // dl() is moved to Removed
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
    ];

    protected $checkCallTimePassByRef = true;

    public function __construct()
    {
        $this->funcTable = new SymbolTable($this->funcTable, SymbolTable::IC);
    }

    /**
     * For another Changes to set whether skip the check for Call-time
     * Pass-by-ref
     */
    public function skipCallTimePassByRef($off)
    {
        $this->checkCallTimePassByRef = !$off;
    }

    public function leaveNode($node)
    {
        // Function call
        if ($this->isDeprecatedFunc($node)) {
            $advice = $this->funcTable->get($node->name);
            if ($advice) {
                $errmsg = sprintf('Function %s() is deprecated, %s', $node->name, $advice);
            } else {
                $errmsg = sprintf('Function %s() is deprecated', $node->name);
            }
            /**
             * {Errmsg}
             * Deprecated: Function {function} is deprecated
             *
             * {Reference}
             * http://php.net/manual/en/migration53.deprecated.php
             */
            $this->addSpot('DEPRECATED', true, $errmsg);

        // Assign new instance
        } elseif ($this->isAssignNewByRef($node)) {
            /**
             * {Description}
             * Assigning the return value of new by reference is now deprecated.
             *
             * {Errmsg}
             * Deprecated: Assigning the return value of new by reference is deprecated
             *
             * {Reference}
             * http://php.net/manual/en/migration53.deprecated.php
             */
            $this->addSpot('DEPRECATED', true, 'Assigning the return value of new by reference is deprecated');

        // Call-time pass-by-reference
        } elseif ($this->checkCallTimePassByRef && $this->isCallTimePassByRef($node)) {
            /**
             * {Description}
             * Call-time pass-by-reference is now deprecated
             *
             * {Reference}
             * http://php.net/manual/en/language.references.pass.php
             * http://php.net/manual/en/migration53.deprecated.php
             */
            $this->addSpot('DEPRECATED', true, 'Call-time pass-by-reference is deprecated');
        }
    }

    protected function isDeprecatedFunc($node)
    {
        return ($node instanceof Expr\FuncCall && $this->funcTable->has($node->name));
    }

    protected function isAssignNewByRef($node)
    {
        return ($node instanceof Expr\AssignRef && $node->expr instanceof Expr\New_);
    }

    protected function isCallTimePassByRef($node)
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
