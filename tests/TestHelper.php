<?php
namespace PhpMigration;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Lexer;
use PhpParser\Parser;

class TestHelper
{
    protected static $parser;

    public static function getParser()
    {
        if (!isset(self::$parser)) {
            self::$parser = new Parser(new Lexer\Emulative);
        }

        return self::$parser;
    }

    public static function getNodeByCode($code, $addtag = true)
    {
        if ($addtag) {
            $code = '<?php '.$code;
        }
        return current(self::getParser()->parse($code));
    }
}
