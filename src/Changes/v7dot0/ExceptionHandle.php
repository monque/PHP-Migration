<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;

class ExceptionHandle extends AbstractChange
{
    protected static $version = '7.0.0';

    public function leaveNode($node)
    {
        /**
         * set_exception_handler() is no longer guaranteed to receive Exception
         * objects
         *
         * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.error-handling
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
