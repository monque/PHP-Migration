<?php
namespace PhpMigration\Changes;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;

abstract class AbstractRemoved extends AbstractChange
{
    protected $tableLoaded = false;

    protected $funcTable;

    protected $constTable;

    public function prepare()
    {
        if (!$this->tableLoaded) {
            $this->loadTable();
            $this->tableLoaded = true;
        }
    }

    public function loadTable()
    {
        if (isset($this->funcTable)) {
            $this->funcTable = new SymbolTable(array_flip($this->funcTable), SymbolTable::IC);
        }
        if (isset($this->constTable)) {
            $this->constTable = new SymbolTable(array_flip($this->constTable), SymbolTable::CS);
        }
    }

    public function leaveNode($node)
    {
        // Function
        if ($this->isRemovedFunc($node)) {
            $this->addSpot('FATAL', true, sprintf('Function %s() is removed', $node->name));

        // Constant
        } elseif ($this->isRemovedConst($node)) {
            $this->addSpot('WARNING', true, sprintf('Constant %s is removed', $node->name));
        }
    }

    public function isRemovedFunc($node)
    {
        return ($node instanceof Expr\FuncCall && isset($this->funcTable) &&
                $this->funcTable->has($node->name));
    }

    public function isRemovedConst($node)
    {
        return ($node instanceof Expr\ConstFetch && isset($this->constTable) &&
                $this->constTable->has($node->name));
    }
}
