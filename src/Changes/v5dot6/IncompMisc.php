<?php
namespace PhpMigration\Changes\v5dot6;

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

class IncompMisc extends Change
{
    protected static $version = '5.6.0';

    public function leaveNode($node)
    {
        // json_decode
        if ($node instanceof Expr\FuncCall && NameHelper::isSameFunc($node->name, 'json_decode')) {
            /**
             * {Description}
             * json_decode() now rejects non-lowercase variants of the JSON 
             * literals true, false and null at all times, as per the JSON 
             * specification, and sets json_last_error() accordingly. 
             * Previously, inputs to json_decode() that consisted solely of one 
             * of these values in upper or mixed case were accepted.
             *
             * This change will only affect cases where invalid JSON was being 
             * passed to json_decode(): valid JSON input is unaffected and will 
             * continue to be parsed normally.
             *
             * {Reference}
             * http://php.net/manual/en/migration56.incompatible.php#migration56.incompatible.json-decode
             */
            $this->addSpot('NOTICE', 'json_decode() rejects non-lowercase variants of true, false, null');
        }
    }
}
