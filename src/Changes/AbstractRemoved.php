<?php
namespace PhpMigration\Changes;

use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;

abstract class AbstractRemoved extends AbstractChange
{
    protected $funcTable;

    protected $constTable;

    protected $varTable;

    public function __construct()
    {
        if (isset($this->funcTable)) {
            $this->funcTable = new SymbolTable($this->funcTable, SymbolTable::IC);
        }
        if (isset($this->constTable)) {
            $this->constTable = new SymbolTable($this->constTable, SymbolTable::CS);
        }
        if (isset($this->varTable)) {
            $this->varTable = new SymbolTable($this->varTable, SymbolTable::CS);
        }
    }

    public function leaveNode($node)
    {
        // Function
        if ($this->isRemovedFunc($node)) {
            $this->addSpot('FATAL', true, sprintf('Function %s() is removed', $node->migName));

        // Constant
        } elseif ($this->isRemovedConst($node)) {
            $this->addSpot('WARNING', true, sprintf('Constant %s is removed', $node->migName));

        // Variable
        } elseif ($this->isRemovedVar($node)) {
            $this->addSpot('WARNING', true, sprintf('Variable $%s is removed', $node->migName));
        }
    }

    protected function isRemovedFunc($node)
    {
        return ($node instanceof Expr\FuncCall && isset($this->funcTable) &&
                $this->funcTable->has($node->migName));
    }

    protected function isRemovedConst($node)
    {
        return ($node instanceof Expr\ConstFetch && isset($this->constTable) &&
                $this->constTable->has($node->migName));
    }

    protected function isRemovedVar($node)
    {
        return ($node instanceof Expr\Variable && isset($this->varTable) &&
                $this->varTable->has($node->migName));
    }
}
