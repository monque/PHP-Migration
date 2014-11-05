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
use PhpMigration\Utils\NameHelper;
use PhpParser\Node\Expr\FuncCall;

class IncompObStart extends Change
{
    protected static $version = '5.4.0';

    public function leaveNode($node)
    {
        if ($node instanceof FuncCall && NameHelper::isSameFunc($node->name, 'ob_start') && isset($node->args[2])) {
            /**
             * {Description}
             * The third parameter of ob_start() has changed from boolean erase
             * to integer flags. Note that code that explicitly set erase to
             * FALSE will no longer behave as expected in PHP 5.4: please
             * follow this example to write code that is compatible with PHP
             * 5.3 and 5.4.
             *
             * {Reference}
             * http://php.net/manual/en/function.ob-start.php
             * http://php.net/manual/en/migration54.incompatible.php
             */
            $this->addSpot('WARNING', 'The third parameter of ob_start() has changed');
        }
    }
}
