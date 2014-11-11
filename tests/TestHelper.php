<?php
namespace PhpMigration;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\CheckVisitor;
use PhpParser\Lexer;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
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

    public static function parseCode($code, $addtag = true)
    {
        if ($addtag) {
            $code = '<?php '.$code;
        }
        return self::getParser()->parse($code);
    }

    public static function getNodeByCode($code, $addtag = true)
    {
        return current(self::parseCode($code, $addtag));
    }

    public static function getProperty($object, $name)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    public static function runChange($change, $code)
    {
        $visitor = new CheckVisitor(array($change));

        $traverser = new NodeTraverser;
        $traverser->addVisitor(new NameResolver);
        $traverser->addVisitor($visitor);

        $visitor->prepare();
        $traverser->traverse(self::parseCode($code));
        $visitor->finish();

        return $visitor->getSpots();
    }
}
