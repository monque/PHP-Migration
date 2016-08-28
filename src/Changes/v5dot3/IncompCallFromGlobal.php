<?php
namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;

class IncompCallFromGlobal extends AbstractChange
{
    protected static $version = '5.3.0';

    protected $funcTable = [
        'func_get_arg', 'func_get_args', 'func_num_args'
    ];

    public function __construct()
    {
        $this->funcTable = new SymbolTable($this->funcTable, SymbolTable::IC);
    }

    protected function emitSpot($node)
    {
        /**
         * {Description}
         * func_get_arg(), func_get_args() and func_num_args() can no longer be
         * called from the outermost scope of a file that has been included by
         * calling include or require from within a function in the calling
         * file.
         *
         * {Errmsg}
         * Warning:  {method} Called from the global scope - no function context
         *
         * {Reference}
         * http://php.net/manual/en/migration53.incompatible.php
         */

        $message = sprintf(
            '%s() Called from the global scope - no function context',
            $node->name
        );
        $this->addSpot('WARNING', true, $message);
    }

    public function enterNode($node)
    {
        // Populate
        if ($node instanceof Expr\FuncCall &&
                $this->funcTable->has($node->name) &&
                !$this->visitor->inFunction()) {
            $this->emitSpot($node);
        }
    }
}
