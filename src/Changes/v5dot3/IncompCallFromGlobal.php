<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;

class IncompCallFromGlobal extends Change
{
    protected static $function = array(
        'func_get_arg', 'func_get_args', 'func_num_args'
    );

    protected function emitSpot($node)
    {
        /*
         * {Description}
         * func_get_arg(), func_get_args() and func_num_args() can no longer be 
         * called from the outermost scope of a file that has been included by 
         * calling include or require from within a function in the calling 
         * file.
         * {Errmsg}
         * Warning:  {method}: Called from the global scope - no function context
         * {Reference}
         * http://php.net/manual/en/migration53.incompatible.php
         */

        $message = sprintf(
            '%s() Called from the global scope - no function context',
            $node->name
        );
        $this->visitor->addSpot($message);
    }

    public function enterNode($node)
    {
        // Populate
        if ($node instanceof Expr\FuncCall && !ParserHelper::isDynamicCall($node)) {
            $namestr = $node->name->toString();
            if (!$this->visitor->inMethod() && !$this->visitor->inFunction() &&
                    in_array($namestr, static::$function)) {
                $this->emitSpot($node);
            }
        }
    }
}
