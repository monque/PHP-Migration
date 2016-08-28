<?php
namespace PhpMigration\Changes;

use PhpMigration\SymbolTable;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

abstract class AbstractKeywordReserved extends AbstractChange
{
    protected $wordTable;

    public function __construct()
    {
        $this->wordTable = new SymbolTable($this->wordTable, SymbolTable::IC);
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
                $node instanceof Expr\MethodCall || $node instanceof Expr\StaticCall ||
                $node instanceof Expr\ConstFetch ||
                ($node instanceof Expr\FuncCall && !ParserHelper::isDynamicCall($node))) {
            $name = $node->migName;
        }
        if (!is_null($name) && $this->wordTable->has($name)) {
            $this->addSpot('FATAL', true, 'Keyword "'.$name.'" is reserved');
        }
    }
}
