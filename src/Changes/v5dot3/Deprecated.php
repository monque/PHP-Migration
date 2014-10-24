<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;

class Deprecated extends Change
{
    protected $version = '5.3.0';

    protected $description = <<<EOT
EOT;

    protected $errmsg = <<<EOT
EOT;

    protected $reference = 'http://php.net/manual/en/migration53.deprecated.php';

    protected $cur_file;

    protected $functions = array(
        'call_user_method'          => 'use call_user_func() instead',
        'call_user_method_array'    => 'use call_user_func_array() instead',
        'define_syslog_variables'   => '',
        'dl'                        => '',
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

    public function beforeTraverse($filename)
    {
        $this->cur_file = $filename;  // TODO: 统一保存
    }

    public function leaveNode($node)
    {
        if ($node instanceof PhpParser\Node\Expr\FuncCall) {
            $this->populateCall($node);
        } elseif ($node instanceof PhpParser\Node\Expr\AssignRef) {
            if ($node->expr instanceof PhpParser\Node\Expr\New_) {
                printf("Deprecated: Assigning the return value of new by reference is deprecated in %s on line %d\n", $this->cur_file, $node->getLine());
            }
        }
        // TODO: call-time pass-by-reference
        // TODO: advice
        // TODO: Change的概念定义、命名规范，是否一个change可以检查多个特征，change是否需要和advice对应
    }

    protected function populateCall($node)
    {
        if (!is_string($node->name) && !($node->name instanceof PhpParser\Node\Name)) {
            return;
        }

        $name = (string) $node->name;
        if (isset($this->functions[$name])) {
            $advice = $this->functions[$name];
            printf("Function %s() is deprecated %s in %s on line %d\n", $name, $advice, $this->cur_file, $node->getLine());
        }
    }
}
