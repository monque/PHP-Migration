<?php
namespace PhpMigration\Changes\v5dot4;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Expr;

class IncompByReference extends AbstractChange
{
    protected static $version = '5.4.0';

    public function prepare()
    {
        $this->visitor->callChange('v5dot3\Deprecated', 'skipCallTimePassByRef', true);
    }

    public function leaveNode($node)
    {
        if ($this->isCallTimePassByRef($node)) {
            /**
             * {Description}
             * Call-time pass by reference has been removed.
             *
             * {Errmsg}
             * Fatal error:  Call-time pass-by-reference has been removed
             *
             * {Reference}
             * http://php.net/manual/en/language.references.pass.php
             * http://php.net/manual/en/migration54.incompatible.php
             */
            $this->addSpot('FATAL', true, 'Call-time pass-by-reference has been removed');
        }
    }

    /**
     * Duplicated with same method in Changes/v5dot3/Deprecated.php
     */
    public function isCallTimePassByRef($node)
    {
        if (!($node instanceof Expr\FuncCall || $node instanceof Expr\StaticCall ||
                $node instanceof Expr\MethodCall)) {
            return false;
        }

        foreach ($node->args as $arg) {
            if ($arg->byRef) {
                return true;
            }
        }
        return false;
    }
}
