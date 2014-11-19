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
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;

class IncompMisc extends AbstractChange
{
    protected static $version = '5.4.0';

    public function leaveNode($node)
    {
        // array_combine()
        if ($node instanceof Expr\FuncCall && ParserHelper::isSameFunc($node->name, 'array_combine')) {
            /**
             * {Description}
             * array_combine() now returns array() instead of FALSE when two empty
             * arrays are provided as parameters.
             *
             * {Reference}
             * http://php.net/manual/en/migration54.incompatible.php
             */
            $this->addSpot('NOTICE', false, 'array_combine() now returns array() instead of FALSE when two empty arrays given');

        // ob_start()
        } elseif ($node instanceof Expr\FuncCall && ParserHelper::isSameFunc($node->name, 'ob_start') &&
                isset($node->args[2])) {
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
            $this->addSpot('WARNING', true, 'The third parameter of ob_start() has changed');

        } elseif ($node instanceof Expr\FuncCall &&
            (ParserHelper::isSameFunc($node->name, 'htmlentities') || ParserHelper::isSameFunc($node->name, 'htmlspecialchars'))) {
            /**
             * {Description}
             * If you use htmlentities() with asian character sets, it works
             * like htmlspecialchars() - this has always been the case in
             * previous versions of PHP, but now an E_STRICT level error is
             * emitted.
             *
             * The default character set for htmlspecialchars() and
             * htmlentities() is now UTF-8, instead of ISO-8859-1. Note that
             * changing your output charset via the default_charset
             * configuration setting does not affect
             * htmlspecialchars/htmlentities unless you are passing "" (an
             * empty string) as the encoding parameter to your
             * htmlspecialchars()/htmlentities() calls. Generally we do not
             * recommend doing this because you should be able to change your
             * output charset without affecting the runtime charset used by
             * these functions. The safest approach is to explicitly set the
             * charset on each call to htmlspecialchars() and htmlentities().
             *
             * {Reference}
             * http://php.net/manual/en/function.htmlentities.php
             * http://php.net/manual/en/function.htmlspecialchars.php
             * http://php.net/manual/en/migration54.other.php
             * http://php.net/manual/en/migration54.incompatible.php
             */
            $level = false;
            $msgbox = array();

            if (ParserHelper::isSameFunc($node->name, 'htmlentities')) {
                $level = 'WARNING';
                $msgbox[] = 'won\'t encode asian character sets';
            }

            if (!isset($node->args[2])) {  // Set encoding
                $level = 'NOTICE';
                $msgbox[] = 'default encoding was changed';
            }

            if ($level) {
                $this->addSpot($level, false, $node->name.'() '.implode(', ', $msgbox));
            }
        }
    }
}
