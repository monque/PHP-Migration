<?php
namespace PhpMigration\Utils;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Logging;
use PhpParser\Node;
use PhpParser\Node\Expr;

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
            return !($node->name instanceof Node\Name);
        } else {
            throw new \Exception('Invalid function, method call node ('.get_class($node).')');
        }
    }
}
