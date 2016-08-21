<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Expr;

class IncompList extends AbstractChange
{
    protected static $version = '7.0.0';

    public function leaveNode($node)
    {
        if ($node instanceof Expr\List_) {
            $this->checkVarOrder($node);

            $this->checkEmpty($node);
        }
    }

    protected function checkVarOrder(Expr\List_ $node)
    {
        // Any var is dim[null]
        foreach ($node->vars as $var) {
            if ($var instanceof Expr\ArrayDimFetch && is_null($var->dim)) {
                $this->addSpot('NOTICE', true, 'list() no longer assigns variables in reverse order');
                return;
            }
        }
    }

    protected function checkEmpty(Expr\List_ $node)
    {
        // All var is null
        foreach ($node->vars as $var) {
            if (!is_null($var)) {
                return;
            }
        }

        $this->addSpot('NOTICE', true, 'empty list() assignments have been removed');
    }
}
