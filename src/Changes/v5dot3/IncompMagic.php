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
use PhpParser\Node\Stmt;

class IncompMagic extends Change
{
    protected static $version = '5.3.0';

    protected static $prepared = false;

    protected static $funcTable = array(
        '__get', '__set', '__isset', '__unset', '__call',
    );

    public function prepare()
    {
        if (!static::$prepared) {
            static::$funcTable  = new SymbolTable(array_flip(static::$funcTable), SymbolTable::IC);
            static::$prepared = true;
        }
    }

    protected function emitNonPub($node)
    {
        /*
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
            $this->visitor->getClassname(),
            $node->name
        );
        $this->addSpot('WARNING', $message, $node->getLine());
    }

    protected function emitToString($node)
    {
        /*
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
            $this->visitor->getClassname()
        );
        $this->addSpot('FATAL', $message, $node->getLine());
    }

    public function leaveNode($node)
    {
        if (!($node instanceof Stmt\Class_)) {
            return;
        }

        foreach ($node->getMethods() as $mnode) {
            if ((!$mnode->isPublic() || $mnode->isStatic()) && static::$funcTable->has($mnode->name)) {
                $this->emitNonPub($mnode);
            } elseif (NameHelper::isSameFunc($mnode->name, '__toString') && count($mnode->params) > 0) {
                $this->emitToString($mnode);
            }
        }
    }
}
