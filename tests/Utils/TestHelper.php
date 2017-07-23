<?php

namespace PhpMigration\Utils;

use PhpMigration\CheckVisitor;
use PhpMigration\ReduceVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

class TestHelper
{
    public static function fetchProperty($object, $name)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($name);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    public static function runChange($change, $code)
    {
        static $traverser_pre, $parser;

        $code = '<?php '.$code;

        $visitor = new CheckVisitor([$change]);

        $traverser = new NodeTraverser();
        $traverser->addVisitor($visitor);

        $visitor->prepare();
        $visitor->setCode($code);

        if (!isset($parser)) {
            $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        }
        $stmts = $parser->parse($code);

        if (!isset($traverser_pre)) {
            $traverser_pre = new NodeTraverser();
            $traverser_pre->addVisitor(new NameResolver());
            $traverser_pre->addVisitor(new ReduceVisitor());
        }
        $stmts = $traverser_pre->traverse($stmts);

        $traverser->traverse($stmts);

        $visitor->finish();

        return $visitor->getSpots();
    }
}
