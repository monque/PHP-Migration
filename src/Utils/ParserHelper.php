<?php
namespace PhpMigration\Utils;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Node;
use PhpParser\Node\Expr;
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
            return !($node->name instanceof Node\Name);
        } else {
            throw new \Exception('Invalid function, method call node ('.get_class($node).')');
        }
    }

    protected static function isConditionalDeclare($node, $testfunc)
    {
        if (!($node instanceof Stmt\If_ && $node->cond instanceof Expr\BooleanNot)) {
            return false;
        }

        $expr = $node->cond->expr;
        return $expr instanceof Expr\FuncCall && NameHelper::isSameFunc($expr->name, $testfunc);
    }

    public static function isConditionalFunc($node)
    {
        return static::isConditionalDeclare($node, 'function_exists');
    }

    public static function isConditionalClass($node)
    {
        return static::isConditionalDeclare($node, 'class_exists');
    }

    public static function getConditionalName($node)
    {
        return $node->cond->expr->args[0]->value->value;
    }
}
