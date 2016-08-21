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
use PhpParser\Node\Scalar;
use PhpParser\Node\Stmt;

abstract class AbstractIntroduced extends AbstractChange
{
    protected $tableLoaded = false;

    protected $funcTable;

    protected $methodTable;

    protected $classTable;

    protected $constTable;

    protected $paramTable;

    protected $condFunc = null;

    protected $condConst = null;

    public function prepare()
    {
        if (!$this->tableLoaded) {
            $this->loadTable();
            $this->tableLoaded = true;
        }
    }

    public function loadTable()
    {
        if (isset($this->funcTable)) {
            $this->funcTable = new SymbolTable(array_flip($this->funcTable), SymbolTable::IC);
        }
        if (isset($this->methodTable)) {
            $this->methodTable  = new SymbolTable(array_flip($this->methodTable), SymbolTable::IC);
        }
        if (isset($this->classTable)) {
            $this->classTable = new SymbolTable(array_flip($this->classTable), SymbolTable::IC);
        }
        if (isset($this->constTable)) {
            $this->constTable = new SymbolTable(array_flip($this->constTable), SymbolTable::CS);
        }
    }

    public function enterNode($node)
    {
        // Support the simplest conditional declaration
        if (ParserHelper::isConditionalFunc($node)) {
            $this->condFunc = ParserHelper::getConditionalName($node);
        } elseif (ParserHelper::isConditionalConst($node)) {
            $this->condConst = ParserHelper::getConditionalName($node);
        }
    }

    public function leaveNode($node)
    {
        // Function
        if ($this->isNewFunc($node)) {
            $this->addSpot('FATAL', true, sprintf('Cannot redeclare %s()', $node->name));

        // Method
        } elseif ($this->isNewMethod($node, $method_name)) {
            $this->addSpot('WARNING', true, sprintf(
                'Method %s::%s() will override built-in method %s()',
                $this->visitor->getClassname(),
                $node->name,
                $method_name
            ));

        // Class, Interface, Trait
        } elseif ($this->isNewClass($node)) {
            /**
             * TODO: We should check namespaced name instead literal
             * Predis/Session/SessionHandler.php in Predis will be affteced
             */
            $this->addSpot('FATAL', true, sprintf('Cannot redeclare class "%s"', $node->name));

        // Constant
        } elseif ($this->isNewConst($node)) {
            $constname = $node->args[0]->value->value;
            $this->addSpot('WARNING', true, sprintf('Constant "%s" already defined', $constname));

        // Parameter
        } elseif ($this->isNewParam($node)) {
            $advice = $this->paramTable->get($node->name);
            $this->addSpot('NEW', false, sprintf('Function %s() has new parameter, %s', $node->name, $advice));
        }

        // Conditional declaration clear
        if (ParserHelper::isConditionalFunc($node)) {
            $this->condFunc = null;
        } elseif (ParserHelper::isConditionalConst($node)) {
            $this->condConst = null;
        }
    }

    protected function isNewFunc($node)
    {
        if (!isset($this->funcTable)) {
            return false;
        }

        return ($node instanceof Stmt\Function_ && $this->funcTable->has($node->name) &&
            (is_null($this->condFunc) || !ParserHelper::isSameFunc($node->name, $this->condFunc)));
    }

    protected function isNewMethod($node, &$mname = null)
    {
        if (!isset($this->methodTable) || !($node instanceof Stmt\ClassMethod)) {
            return false;
        }
        $name = $this->visitor->getClass()->extends;
        if (!$name) {
            return false;
        }
        $mname = $name.'::'.$node->name;
        return $this->methodTable->has($mname);
    }

    protected function isNewClass($node)
    {
        if (!isset($this->classTable)) {
            return false;
        }

        return (($node instanceof Stmt\ClassLike) &&
                $this->classTable->has($node->namespacedName->toString()));
    }

    protected function isNewConst($node)
    {
        if (!isset($this->constTable)) {
            return false;
        }

        if ($node instanceof Expr\FuncCall && ParserHelper::isSameFunc($node->name, 'define') &&
                $node->args[0]->value instanceof Scalar\String_) {
            $constname = $node->args[0]->value->value;
            return $this->constTable->has($constname) &&
                    (is_null($this->condConst) || $constname != $this->condConst);
        }
        return false;
    }

    protected function isNewParam($node)
    {
        return ($node instanceof Expr\FuncCall && isset($this->paramTable) &&
                $this->paramTable->has($node->name));
    }
}
