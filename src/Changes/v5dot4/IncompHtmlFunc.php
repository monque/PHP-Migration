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
use PhpParser\Node\Expr;

class IncompHtmlFunc extends Change
{
    protected static $version = '5.4.0';

    public function leaveNode($node)
    {
        if ($node instanceof Expr\FuncCall) {
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
            if (NameHelper::isSameFunc($node->name, 'htmlentities')) {
                $this->addSpot('WARNING', 'htmlentities() never encode asian character sets, and default encoding was changed');
            } elseif (NameHelper::isSameFunc($node->name, 'htmlspecialchars')) {
                $this->addSpot('NOTICE', 'htmlspecialchars() default encoding was changed');
            }
        }
    }
}
