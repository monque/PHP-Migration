<?php
namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Stmt;

class IncompMagic extends AbstractChange
{
    protected static $version = '5.3.0';

    protected $funcTable = [
        '__get', '__set', '__isset', '__unset', '__call',
    ];

    public function __construct()
    {
        $this->funcTable = new SymbolTable($this->funcTable, SymbolTable::IC);
    }

    protected function emitNonPub($node)
    {
        /**
         * {Description}
         * The magic methods __get(), __set(), __isset(), __unset(), and
         * __call() must always be public and can no longer be static. Method
         * signatures are now enforced.
         *
         * {Errmsg}
         * Warning: The magic method {method} must have public visibility and cannot be static
         *
         * {Reference}
         * http://php.net/manual/en/migration53.incompatible.php
         */

        $message = sprintf(
            'The magic method %s::%s() must have public visibility and cannot be static',
            $this->visitor->getClassName(),
            $node->name
        );
        $this->addSpot('WARNING', true, $message, $node->getLine());
    }

    protected function emitToString($node)
    {
        /**
         * {Description}
         * The __toString() magic method can no longer accept arguments.
         *
         * {Errmsg}
         * Fatal error: Method {class}::__tostring() cannot take arguments
         *
         * {Reference}
         * http://php.net/manual/en/migration53.incompatible.php
         */

        $message = sprintf(
            'Method %s::__tostring() cannot take arguments',
            $this->visitor->getClassName()
        );
        $this->addSpot('FATAL', true, $message, $node->getLine());
    }

    public function leaveNode($node)
    {
        if (!($node instanceof Stmt\Class_)) {
            return;
        }

        foreach ($node->getMethods() as $mnode) {
            if ((!$mnode->isPublic() || $mnode->isStatic()) && $this->funcTable->has($mnode->name)) {
                $this->emitNonPub($mnode);
            } elseif (ParserHelper::isSameFunc($mnode->name, '__toString') && count($mnode->params) > 0) {
                $this->emitToString($mnode);
            }
        }
    }
}
