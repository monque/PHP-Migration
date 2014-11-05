<?php
namespace PhpMigration\Changes\v5dot4;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpParser\Node\Scalar\LNumber;
use PhpParser\Node\Stmt\Break_;
use PhpParser\Node\Stmt\Continue_;

class IncompBreakContinue extends Change
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

        if ($node instanceof Break_) {
            $operator = 'break';
        } elseif ($node instanceof Continue_) {
            $operator = 'continue';
        } else {
            return;
        }

        if (!is_null($node->num) && !($node->num instanceof LNumber)) {
            $this->addSpot('FATAL', $operator.' operator with non-constant operand is no longer supported');
        } elseif ($node->num instanceof LNumber && $node->num->value < 1) {
            $this->addSpot('FATAL', $operator.' operator accepts only positive numbers');
        }
    }
}
