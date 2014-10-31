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
use PhpMigration\Utils\NameHelper;
use PhpParser\Node\Expr;

class IncompMisc extends Change
{
    protected static $prepared = false;

    protected static $arrFuncTable = array(
        'natsort', 'natcasesort', 'usort', 'uasort', 'uksort', 'array_flip', 'array_unique',
    );

    public function prepare()
    {
        if (!static::$prepared) {
            static::$arrFuncTable  = new SymbolTable(array_flip(static::$arrFuncTable), SymbolTable::IC);
            static::$prepared = true;
        }
    }

    public function leaveNode($node)
    {
        if ($node instanceof Expr\FuncCall) {
            // clearstatcache()
            if (NameHelper::isSameFunc($node->name, 'clearstatcache')) {
                $this->visitor->addSpot('clearstatcache() no longer clears the realpath cache by default');

            // realpath()
            } elseif (NameHelper::isSameFunc($node->name, 'realpath')) {
                $this->visitor->addSpot('realpath() is now fully platform-independent. Consequence of this is that invalid relative paths such as __FILE__ . "/../x" do not work anymore.');

            // array functions
            } elseif (static::$arrFuncTable->has($node->name)) {
                $this->visitor->addSpot(sprintf('%s() no longer accept objects passed as arguments', $node->name));

            // call_user_func_array()
            } elseif (NameHelper::isSameFunc($node->name, 'call_user_func_array')) {
                if (!($node->args[1]->value instanceof Expr\Array_)) {
                    // TODO: comment this, it's from User Contributed Notes in php.net
                    $this->visitor->addSpot(sprintf('%s() no longer accept non-array passed as arguments', $node->name));
                }
            }
        }
    }
}
