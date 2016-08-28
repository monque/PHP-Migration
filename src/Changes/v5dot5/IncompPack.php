<?php
namespace PhpMigration\Changes\v5dot5;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class IncompPack extends AbstractChange
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
        if ($node instanceof Expr\FuncCall && ParserHelper::isSameFunc($node->name, 'unpack')) {
            $affected = true;
            $certain = false;

            if (!isset($node->args[0])) {
                return;
            }
            $format = $node->args[0]->value;

            // Try to check arg $format
            if ($format instanceof Scalar\String_) {
                // using stripos for both "a" and "A"
                $certain = $affected = (stripos($format->value, 'a') !== false);
            }

            if ($affected) {
                $this->addSpot('WARNING', $certain, 'Behavior of pack() with "a", "A" in format is changed');
            }
        }
    }
}
