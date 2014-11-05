<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;

class IncompCallFromGlobal extends Change
{
    protected static $version = '5.3.0';

    protected static $prepared = false;

    protected static $funcTable = array(
        'func_get_arg', 'func_get_args', 'func_num_args'
    );

    public function prepare()
    {
        if (!static::$prepared) {
            static::$funcTable  = new SymbolTable(array_flip(static::$funcTable), SymbolTable::IC);
            static::$prepared = true;
        }
    }

    protected function emitSpot($node)
    {
        /*
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
        $this->addSpot('WARNING', $message);
    }

    public function enterNode($node)
    {
        // Populate
        if ($node instanceof Expr\FuncCall &&
                static::$funcTable->has($node->name) &&
                !$this->visitor->inMethod() &&
                !$this->visitor->inFunction()) {
            $this->emitSpot($node);
        }
    }
}
