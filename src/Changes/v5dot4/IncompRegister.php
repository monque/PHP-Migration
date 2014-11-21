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

class IncompRegister extends AbstractChange
{
    protected static $version = '5.4.0';

    public function leaveNode($node)
    {
        /**
         * {Description}
         * The register_globals and register_long_arrays php.ini directives
         * have been removed.
         *
         * {Reference}
         * http://php.net/manual/en/migration54.incompatible.php
         */
        if ($node instanceof Expr\Variable && is_string($node->name) &&
                preg_match('/^HTTP_[a-zA-Z_]+?_VARS$/', $node->name)) {
            $this->addSpot(
                'WARNING',
                true,
                'The register_long_arrays is removed, $'.$node->name.' no longer available'
            );
        }
    }
}
