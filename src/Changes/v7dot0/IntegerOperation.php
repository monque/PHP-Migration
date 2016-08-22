<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class IntegerOperation extends AbstractChange
{
    protected static $version = '7.0.0';

    public function leaveNode($node)
    {
        /**
         * Negative bitshifts
         *
         * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.integers.negative-bitshift
         */
        if ($node instanceof Expr\BinaryOp\ShiftLeft || $node instanceof Expr\BinaryOp\ShiftRight) {
            $affect = true;
            $certain = false;

            if ($node->right instanceof Scalar\LNumber) {
                $affect = false;
            } elseif ($node->right instanceof Expr\UnaryMinus && $node->right->expr instanceof Scalar\LNumber) {
                $certain = true;
            }

            if ($affect) {
                $this->addSpot('NOTICE', $certain, 'Bitwise shifts should not by negative numbers');
            }

        /**
         * Changes to Modulus By Zero
         *
         * The modulus operator E_WARNING has been removed and will throw a DivisionByZeroError exception.
         *
         * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.integers.div-by-zero
         */
        } elseif ($node instanceof Expr\BinaryOp\Mod) {
            $affect = true;
            $certain = false;

            if ($node->right instanceof Scalar\LNumber) {
                $certain = true;
                $affect = ($node->right->value == 0);
            }

            if ($affect) {
                $this->addSpot('NOTICE', $certain, 'Modulus operator will throw a exception if divisor is 0');
            }
        }
    }
}
