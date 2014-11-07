<?php
namespace PhpMigration\Changes;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpMigration\Utils\NameHelper;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

abstract class AbstractIntroduced extends Change
{
    protected $tableLoaded = false;

    protected $funcTable;

    protected $classTable;

    protected $constTable;

    protected $paramTable;

    protected $condFunc = null;

    protected $condClass = null;

    abstract protected function loadTable();

    public function prepare()
    {
        if (!$this->tableLoaded) {
            $this->loadTable();
            $this->tableLoaded = true;
        }
    }

    public function enterNode($node)
    {
        // Support the simplest conditional declaration
        if (ParserHelper::isConditionalFunc($node)) {
            $this->condFunc = ParserHelper::getConditionalName($node);
        } elseif (ParserHelper::isConditionalClass($node)) {
            $this->condClass = ParserHelper::getConditionalName($node);
        }
    }

    public function leaveNode($node)
    {
        // Function
        if ($this->isNewFunc($node)) {
            $this->addSpot('FATAL', sprintf('Cannot redeclare %s()', $node->name));

        // Class
        } elseif ($this->isNewClass($node)) {
            $this->addSpot('FATAL', sprintf('Cannot redeclare class %s', $node->name));

        // Constant
        } elseif ($this->isNewConst($node)) {
            $constname = $node->args[0]->value->value;
            $this->addSpot('WARNING', sprintf('Constant %s already defined', $constname));

        // Parameter
        } elseif ($this->isNewParam($node)) {
            $advice = $this->paramTable->get($node->name);
            $this->addSpot('NEW', sprintf('Function %s() has new parameter, %s', $node->name, $advice));
        }

        // Conditional declaration clear
        if (ParserHelper::isConditionalFunc($node)) {
            $this->condFunc = null;
        } elseif (ParserHelper::isConditionalClass($node)) {
            $this->condClass = null;
        }
    }

    public function isNewFunc(Node $node)
    {
        return ($node instanceof Stmt\Function_ &&
                (isset($this->funcTable) && $this->funcTable->has($node->name)) &&
                (is_null($this->condFunc) || !NameHelper::isSameFunc($node->name, $this->condFunc)));
    }

    public function isNewClass(Node $node)
    {
        return ($node instanceof Stmt\Class_ &&
                (isset($this->classTable) && $this->classTable->has($node->name)) &&
                (is_null($this->condClass) || !NameHelper::isSameClass($node->name, $this->condClass)));
    }

    public function isNewConst(Node $node)
    {
        if ($node instanceof Expr\FuncCall && isset($this->constTable) &&
                NameHelper::isSameFunc($node->name, 'define')) {
            $constname = $node->args[0]->value->value;
            return $this->constTable->has($constname);
        }
        return false;
    }

    public function isNewParam(Node $node)
    {
        return ($node instanceof Expr\FuncCall && isset($this->paramTable) &&
                $this->paramTable->has($node->name));
    }
}
