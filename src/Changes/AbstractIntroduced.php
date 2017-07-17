<?php

namespace PhpMigration\Changes;

use PhpMigration\SymbolTable;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

abstract class AbstractIntroduced extends AbstractChange
{
    protected $funcTable;

    protected $methodTable;

    protected $classTable;

    protected $constTable;

    protected $paramTable;

    protected $condFunc = null;

    protected $condConst = null;

    public function __construct()
    {
        if (isset($this->funcTable)) {
            $this->funcTable = new SymbolTable($this->funcTable, SymbolTable::IC);
        }
        if (isset($this->methodTable)) {
            $this->methodTable = new SymbolTable($this->methodTable, SymbolTable::IC);
        }
        if (isset($this->classTable)) {
            $this->classTable = new SymbolTable($this->classTable, SymbolTable::IC);
        }
        if (isset($this->constTable)) {
            $this->constTable = new SymbolTable($this->constTable, SymbolTable::CS);
        }
    }

    public function enterNode($node)
    {
        // Support the simplest conditional declaration
        if ($this->isConditionalFunc($node)) {
            $this->condFunc = $this->getConditionalName($node);
        } elseif ($this->isConditionalConst($node)) {
            $this->condConst = $this->getConditionalName($node);
        }
    }

    public function leaveNode($node)
    {
        // Function
        if ($this->isNewFunc($node)) {
            $this->addSpot('FATAL', true, sprintf('Cannot redeclare %s()', $node->migName));

        // Method
        } elseif ($this->isNewMethod($node, $method_name)) {
            $this->addSpot('WARNING', true, sprintf(
                'Method %s::%s() will override built-in method %s()',
                $this->visitor->getClassName(),
                $node->migName,
                $method_name
            ));

        // Class, Interface, Trait
        } elseif ($this->isNewClass($node)) {
            $this->addSpot('FATAL', true, sprintf('Cannot redeclare class "%s"', $node->migName));

        // Constant
        } elseif ($this->isNewConst($node)) {
            $constname = $node->args[0]->value->value;
            $this->addSpot('WARNING', true, sprintf('Constant "%s" already defined', $constname));

        // Parameter
        } elseif ($this->isNewParam($node)) {
            $advice = $this->paramTable->get($node->migName);
            $this->addSpot('NEW', false, sprintf('Function %s() has new parameter, %s', $node->migName, $advice));
        }

        // Conditional declaration clear
        if ($this->isConditionalFunc($node)) {
            $this->condFunc = null;
        } elseif ($this->isConditionalConst($node)) {
            $this->condConst = null;
        }
    }

    protected function isNewFunc($node)
    {
        if (!isset($this->funcTable) || !$node instanceof Stmt\Function_ || !is_string($node->migName)) {
            return;
        }

        return $this->funcTable->has($node->migName) &&
            (is_null($this->condFunc) || !ParserHelper::isSameFunc($node->migName, $this->condFunc));
    }

    protected function isNewMethod($node, &$mname = null)
    {
        if (!isset($this->methodTable) || !$node instanceof Stmt\ClassMethod) {
            return false;
        }

        $class = $this->visitor->getClass();
        if (!$class instanceof Stmt\Class_ || !$class->migExtends) {
            return false;
        }

        $mname = $class->migExtends.'::'.$node->migName;

        return $this->methodTable->has($mname);
    }

    protected function isNewClass($node)
    {
        if (!isset($this->classTable) || !$node instanceof Stmt\ClassLike || is_null($node->migName)) {
            return false;
        }

        return $this->classTable->has($node->migName);
    }

    protected function isNewConst($node)
    {
        if (!isset($this->constTable) ||
                !$node instanceof Expr\FuncCall ||
                !ParserHelper::isSameFunc($node->migName, 'define') ||
                !$node->args[0]->value instanceof Scalar\String_) {
            return false;
        }

        $name = $node->args[0]->value->value;

        return $this->constTable->has($name) &&
                (is_null($this->condConst) || $name != $this->condConst);
    }

    protected function isNewParam($node)
    {
        return $node instanceof Expr\FuncCall && isset($this->paramTable) &&
                $this->paramTable->has($node->migName);
    }

    /**
     * Conditional checking.
     */
    protected function isConditionalDeclare($node, $testfunc)
    {
        if (!$node instanceof Stmt\If_ || !$node->cond instanceof Expr\BooleanNot) {
            return false;
        }

        $expr = $node->cond->expr;

        return $expr instanceof Expr\FuncCall && ParserHelper::isSameFunc($expr->migName, $testfunc);
    }

    protected function isConditionalFunc($node)
    {
        return $this->isConditionalDeclare($node, 'function_exists');
    }

    protected function isConditionalConst($node)
    {
        return $this->isConditionalDeclare($node, 'defined');
    }

    protected function getConditionalName($node)
    {
        if ($node->cond->expr->args[0]->value instanceof Scalar\String_) {
            return $node->cond->expr->args[0]->value->value;
        } else {
            return;
        }
    }
}
