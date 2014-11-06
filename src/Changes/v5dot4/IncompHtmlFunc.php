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

class IncompHtmlFunc extends Change
{
    protected static $version = '5.4.0';

    public function leaveNode($node)
    {
        if ($node instanceof FuncCall) {
            /**
             * {Description}
             * If you use htmlentities() with asian character sets, it works 
             * like htmlspecialchars() - this has always been the case in 
             * previous versions of PHP, but now an E_STRICT level error is 
             * emitted.
             *
             * PHP 5.4 and 5.5 will use UTF-8 as the default. Earlier versions 
             * of PHP use ISO-8859-1.
             *
             * Although the encoding argument is technically optional, you are 
             * highly encouraged to specify the correct value for your code if 
             * you are using PHP 5.5 or earlier, or if your default_charset 
             * configuration option may be set incorrectly for the given input.
             *
             * Changelog:
             * - The default value for the encoding parameter was changed to 
             * UTF-8.
             * - The constants ENT_SUBSTITUTE, ENT_DISALLOWED, ENT_HTML401, 
             * ENT_XML1, ENT_XHTML and ENT_HTML5 were added.
             *
             * {Reference}
             * http://php.net/manual/en/function.htmlentities.php
             * http://php.net/manual/en/function.htmlspecialchars.php
             * http://php.net/manual/en/migration54.incompatible.php
             */
            if (NameHelper::isSameFunc($node->name, 'htmlentities')) {
                $this->addSpot('WARNING', 'htmlentities() never encode asian character sets, and default encoding was changed');
            } elseif (NameHelper::isSameFunc($node->name, 'htmlspecialchars')) {
                $this->addSpot('WARNING', 'htmlspecialchars() default encoding was changed');
            }
        }
    }
}
