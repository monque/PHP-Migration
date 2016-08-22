<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

/**
 * foreach no longer changes the internal array pointer
 *
 * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.foreach.array-pointer
 */
class ForeachLoop extends AbstractChange
{
    protected static $version = '7.0.0';

    protected $depth;

    /**
     * No need check for end(), reset(), each(), next(), prev(), they all
     * manipulate pointer by themself.
     */
    protected $funcTable = [
        'current', 'key',
    ];

    public function prepare()
    {
        $this->funcTable = new SymbolTable(array_flip($this->funcTable), SymbolTable::IC);
    }

    public function beforeTraverse(array $nodes)
    {
        $this->depth = 0;
    }

    public function enterNode($node)
    {
        if ($node instanceof Stmt\Foreach_ && $node->byRef) {
            $this->depth++;
        }
    }

    public function leaveNode($node)
    {
        if ($this->depth > 0 && $node instanceof Expr\FuncCall && $this->funcTable->has($node->name)) {
            $this->addSpot('NOTICE', true, 'foreach no longer changes the internal array pointer');
        }

        if ($node instanceof Stmt\Foreach_ && $node->byRef) {
            $this->depth--;
        }
    }
}
