<?php
namespace PhpMigration\Changes\v5dot5;

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
use PhpParser\Node\Scalar;

class IncompPack extends Change
{
    protected static $version = '5.5.0';

    public function leaveNode($node)
    {
        /**
         * {Description}
         * Changes were made to pack() and unpack() to make them more compatible with Perl:
         *
         * pack() now supports the "Z" format code, which behaves identically
         * to "a".
         *
         * unpack() now support the "Z" format code for NULL padded strings,
         * and behaves as "a" did in previous versions: it will strip trailing
         * NULL bytes.
         *
         * unpack() now keeps trailing NULL bytes when the "a" format code is
         * used.
         *
         * unpack() now strips all trailing ASCII whitespace when the "A"
         * format code is used.
         *
         * {Reference}
         * http://php.net/manual/en/migration55.incompatible.php#migration55.incompatible.pack
         */
        if ($node instanceof Expr\FuncCall && NameHelper::isSameFunc($node->name, 'unpack')) {
            $affected = true;
            $format = $node->args[0]->value;

            // Try to check arg $format 
            if ($format instanceof Scalar\String && stripos($format->value, 'a') === false) {
                $affected = false;
            }

            if ($affected) {
                $this->addSpot('WARNING', 'Behavior of pack() with "a", "A" in format is changed');
            }
        }
    }
}

