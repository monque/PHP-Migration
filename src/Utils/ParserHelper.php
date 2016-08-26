<?php
namespace PhpMigration\Utils;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

class ParserHelper
{
    public static function isDynamicCall(Node $node)
    {
        /**
         * Due to the mechanism of dynamic script programming language,
         * it's TOO hard to guess what the callname exactly references to.
         * eg: $_GET['func']($arg)
         */

        if ($node instanceof Expr\MethodCall || $node instanceof Expr\StaticCall) {
            return !is_string($node->name);
        } elseif ($node instanceof Expr\FuncCall) {
            return !($node->name instanceof Name);
        } else {
            throw new \Exception('Invalid function, method call node ('.get_class($node).')');
        }
    }

    public static function isSameFunc($name, $const)
    {
        if (!is_string($name) && !method_exists($name, '__toString')) {
            return false;
        }

        return strcasecmp($name, $const) === 0;
    }

    public static function isSameClass($name, $const)
    {
        if (!is_string($name) && !method_exists($name, '__toString')) {
            return false;
        }

        return strcasecmp($name, $const) === 0;
    }
}
