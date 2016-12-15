<?php

namespace PhpMigration\Utils;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class ParserHelper
{
    public static function isDynamicCall(Node $node)
    {
        /**
         * Due to the mechanism of dynamic script programming language,
         * it's TOO hard to guess what the callname exactly references to.
         * eg: $_GET['func']($arg)
         */
        if ($node instanceof Expr\MethodCall) {
            return !is_string($node->name);
        } elseif ($node instanceof Expr\StaticCall) {
            return !is_string($node->name) || !self::isConstName($node->class);
        } elseif ($node instanceof Expr\FuncCall) {
            return !($node->name instanceof Name);
        } else {
            throw new \Exception('Invalid function, method call node ('.get_class($node).')');
        }
    }

    /**
     * Test if given variable is a const name
     */
    public static function isConstName($prop)
    {
        return is_string($prop) || method_exists($prop, '__toString');
    }

    public static function isSameFunc($name, $const)
    {
        if (!self::isConstName($name)) {
            return false;
        }

        return strcasecmp($name, $const) === 0;
    }

    public static function isSameClass($name, $const)
    {
        if (!self::isConstName($name)) {
            return false;
        }

        return strcasecmp($name, $const) === 0;
    }
}
