<?php
namespace PhpMigration\Changes\v7dot0;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 */

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Expr;

class IncompInteger extends AbstractChange
{
    protected static $version = '7.0.0';

    public function leaveNode($node)
    {
        if ($node instanceof Expr\BinaryOp\ShiftLeft || 
                $node instanceof Expr\BinaryOp\ShiftRight) {

            $certain = ($node->right instanceof Expr\UnaryMinus);
            $this->addSpot('NOTICE', $certain, 'Bitwise shifts should not by negative numbers');
        } elseif ($node instanceof Expr\BinaryOp\Mod) {
            $this->addSpot('NOTICE', false, 'Modulus operator will throw a exception if 0 was divisor');
        }
    }
}
