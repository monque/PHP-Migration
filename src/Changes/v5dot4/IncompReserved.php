<?php
namespace PhpMigration\Changes\v5dot4;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpMigration\SymbolTable;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class IncompReserved extends Change
{
    protected static $version = '5.4.0';

    protected static $prepared = false;

    protected static $keywords = array(
        'trait', 'callable', 'insteadof'
    );

    public function prepare()
    {
        if (!static::$prepared) {
            static::$keywords = new SymbolTable(array_flip(static::$keywords), SymbolTable::IC);
            static::$prepared = true;
        }
    }

    public function leaveNode($node)
    {
        /**
         * {Description}
         * The following keywords are now reserved, and you cannot use any of
         * the following words as constants, class names, function or method
         * names. Using them as variable names is generally OK, but could lead
         * to confusion.
         * trait, callable, insteadof
         *
         * {Reference}
         * http://php.net/manual/en/reserved.keywords.php
         * http://php.net/manual/en/migration54.incompatible.php
         */
        $name = null;
        if ($node instanceof Stmt\Class_ || $node instanceof Stmt\Interface_ ||
                $node instanceof Stmt\Function_ || $node instanceof Stmt\ClassMethod ||
                $node instanceof Expr\MethodCall || $node instanceof Expr\StaticCall) {
            $name = $node->name;
        } elseif ($node instanceof Expr\ConstFetch ||
                ($node instanceof Expr\FuncCall && !ParserHelper::isDynamicCall($node))) {
            $name = $node->name->toString();
        }
        if (!is_null($name) && static::$keywords->has($name)) {
            $this->addSpot('FATAL', 'Keyword '.$name.' is reserved');
        }
    }
}
