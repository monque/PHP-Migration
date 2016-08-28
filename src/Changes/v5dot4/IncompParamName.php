<?php

namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpParser\Node\Stmt;

class IncompParamName extends AbstractChange
{
    protected static $version = '5.4.0';

    protected $autoGlobals = [
        '_SESSION', '_GET', '_POST', '_COOKIE', '_SERVER', '_ENV', '_REQUEST', '_FILES',
    ];

    public function __construct()
    {
        $this->autoGlobals = new SymbolTable($this->autoGlobals, SymbolTable::CS);
    }

    public function leaveNode($node)
    {
        /**
         * {Description}
         * Parameter names that shadow super globals now cause a fatal error.
         * This prohibits code like function foo($_GET, $_POST) {}.
         *
         * {Errmsg}
         * Fatal error: Cannot re-assign auto-global variable
         * Fatal error: Cannot re-assign $this
         *
         * {Reference}
         * http://php.net/manual/en/migration54.incompatible.php
         */
        if (($node instanceof Stmt\Function_ || $node instanceof Stmt\ClassMethod)
                && $this->hasParamShadowGlobal($node)) {
            $this->addSpot('FATAL', true, 'Cannot re-assign auto-global variable');
        }
    }

    protected function hasParamShadowGlobal($node)
    {
        foreach ($node->params as $param) {
            // auto-global
            if ($this->autoGlobals->has($param->name)) {
                return true;

            // $this
            } elseif ($param->name == 'this' && $node instanceof Stmt\ClassMethod && !$node->isStatic()) {
                return true;
            }
        }
        return false;
    }
}
