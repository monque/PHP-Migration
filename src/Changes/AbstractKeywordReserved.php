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
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

abstract class AbstractKeywordReserved extends AbstractChange
{
    protected $tableLoaded = false;

    protected $wordTable;

    public function prepare()
    {
        if (!$this->tableLoaded) {
            $this->wordTable = new SymbolTable(array_flip($this->wordTable), SymbolTable::IC);
            $this->tableLoaded = true;
        }
    }

    public function leaveNode($node)
    {
        /**
         * {Description}
         * These words have special meaning in PHP. Some of them represent
         * things which look like functions, some look like constants, and so
         * on - but they're not, really: they are language constructs. You
         * cannot use any of the following words as constants, class names,
         * function or method names. Using them as variable names is generally
         * OK, but could lead to confusion.
         *
         * {Reference}
         * http://php.net/manual/en/reserved.keywords.php
         */
        $name = null;
        if ($node instanceof Stmt\ClassLike ||
                $node instanceof Stmt\Function_ || $node instanceof Stmt\ClassMethod ||
                $node instanceof Expr\MethodCall || $node instanceof Expr\StaticCall) {
            $name = $node->name;
        } elseif ($node instanceof Expr\ConstFetch ||
                ($node instanceof Expr\FuncCall && !ParserHelper::isDynamicCall($node))) {
            $name = $node->name->toString();
        }
        if (!is_null($name) && $this->wordTable->has($name)) {
            $this->addSpot('FATAL', true, 'Keyword '.$name.' is reserved');
        }
    }
}
