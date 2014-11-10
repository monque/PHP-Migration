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
use PhpMigration\Utils\NameHelper;
use PhpParser\Node\Expr;

class Removed extends Change
{
    protected static $version = '5.3.0';

    public function leaveNode($node)
    {
        // Function call
        if ($node instanceof Expr\FuncCall && NameHelper::isSameFunc($node->name, 'dl')) {
            /*
             * {Description}
             * The dl() function is now disabled by default, and is now 
             * available only under the CLI, CGI, and embed SAPIs.
             *
             * {Reference}
             * http://php.net/manual/en/migration53.sapi.php
             */
            $this->addSpot('FATAL', 'Function dl() is disabled by default');
        }
    }
}
