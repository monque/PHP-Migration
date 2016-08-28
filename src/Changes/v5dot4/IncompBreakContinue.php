<?php

namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

class IncompBreakContinue extends AbstractChange
{
    protected static $version = '5.4.0';

    public function leaveNode($node)
    {
        /**
         * {Description}
         * The break and continue statements no longer accept variable
         * arguments (e.g., break 1 + foo() * $bar;). Static arguments
         * still work, such as break 2;. As a side effect of this change
         * break 0; and continue 0; are no longer allowed.
         *
         * {Errmsg}
         * Fatal error: 'break' operator with non-constant operand is no longer supported
         * Fatal error: 'break' operator accepts only positive numbers
         *
         * {Reference}
         * http://php.net/manual/en/control-structures.continue.php
         * http://php.net/manual/en/control-structures.break.php
         * http://php.net/manual/en/migration54.incompatible.php
         */
        if ($node instanceof Stmt\Break_) {
            $operator = 'break';
        } elseif ($node instanceof Stmt\Continue_) {
            $operator = 'continue';
        } else {
            return;
        }

        if (!is_null($node->num) && !($node->num instanceof Scalar\LNumber)) {
            $this->addSpot('FATAL', true, $operator.' operator with non-constant operand is no longer supported');
        } elseif ($node->num instanceof Scalar\LNumber && $node->num->value < 1) {
            $this->addSpot('FATAL', true, $operator.' operator accepts only positive numbers');
        }
    }
}
