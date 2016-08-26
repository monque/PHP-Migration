<?php
namespace PhpMigration\Utils;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\CheckVisitor;
use PhpMigration\ReduceVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

class TestHelper
{
    protected static $parser;

    public static function getParser()
    {
        if (!isset(self::$parser)) {
            self::$parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        }

        return self::$parser;
    }

    public static function fetchProperty($object, $name)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    public static function runChange($change, $code)
    {
        $code = '<?php '.$code;

        $visitor = new CheckVisitor(array($change));

        $traverser = new NodeTraverser;
        $traverser->addVisitor(new NameResolver);
        $traverser->addVisitor(new ReduceVisitor);
        $traverser->addVisitor($visitor);

        $visitor->prepare();
        $visitor->setCode($code);
        $stmts = self::getParser()->parse($code);
        $traverser->traverse($stmts);
        $visitor->finish();

        return $visitor->getSpots();
    }
}
