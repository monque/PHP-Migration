<?php
namespace PhpMigration\Changes\v7dot0;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 */

use PhpMigration\Changes\AbstractChange;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;

class IncompErrorHandle extends AbstractChange
{
    protected static $version = '7.0.0';

    public function leaveNode($node)
    {
        /**
         * {Description}
         * set_exception_handler() is no longer guaranteed to receive Exception
         * objects
         *
         * Code that implements an exception handler registered with
         * set_exception_handler() using a type declaration of Exception will
         * cause a fatal error when an Error object is thrown.
         *
         * If the handler needs to work on both PHP 5 and 7, you should remove
         * the type declaration from the handler, while code that is being
         * migrated to work on PHP 7 exclusively can simply replace the
         * Exception type declaration with Throwable instead.
         *
         * {Reference}
         * http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.error-handling
         */
        if ($node instanceof Expr\FuncCall && ParserHelper::isSameFunc($node->name, 'set_exception_handler')) {
            if (!isset($node->args[0])) {
                return;
            }

            $affected = true;
            $certain = false;
            $callback = $node->args[0]->value;

            if ($callback instanceof Expr\Closure) {
                if (!isset($callback->params[0]) || !isset($callback->params[0]->type)) {
                    $affected = false;
                    $certain = true;
                } elseif (ParserHelper::isSameClass($callback->params[0]->type, 'Exception')) {
                    $affected = true;
                    $certain = true;
                }
            }

            if ($affected) {
                $this->addSpot('WARNING', $certain, 'set_exception_handler() is no longer guaranteed to receive Exception objects');
            }
        }
    }
}
