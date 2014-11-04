<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\ChangeVisitor;
use PhpMigration\TestHelper;

class IncompByReferenceTest extends \PHPUnit_Framework_TestCase
{
    protected $change;

    protected function setUp()
    {
        $this->change = new IncompByReference();
        $this->change->prepare();
    }

    public function testPopulateDefine()
    {
        // Func
        $name = 'phpmig_none';
        $node = TestHelper::getNodeByCode('function '.$name.'($none) {}');
        $this->change->populateDefine($node, 'func');
        $this->assertFalse(IncompByReference::$declareTable->has($name));

        $name = 'phpmig_first';
        $node = TestHelper::getNodeByCode('function '.$name.'(&$o) {}');
        $this->change->populateDefine($node, 'func');
        $this->assertTrue(IncompByReference::$declareTable->has($name));

        $name = 'phpmig_second';
        $node = TestHelper::getNodeByCode('function '.$name.'($o, &$t) {}');
        $this->change->populateDefine($node, 'func');
        $this->assertTrue(IncompByReference::$declareTable->has($name));

        // Emulate entering class
        $classnode = TestHelper::getNodeByCode('class phpmig {}');
        $visitor = new ChangeVisitor();
        $visitor->enterNode($classnode);
        $this->change->setVisitor($visitor);

        // Method
        $code = <<<'EOC'
class phpmig
{
    static function snone($o) {}
    static function sfirst(&$o) {}
    static function ssecond($o, &$t) {}
    function none($o) {}
    function first(&$o) {}
    function second($o, &$t) {}
}
EOC;
        $node = TestHelper::getNodeByCode($code);
        foreach ($node->getMethods() as $mnode) {
            $this->change->populateDefine($mnode, 'method');
        }
        $this->assertFalse(IncompByReference::$declareTable->has('phpmig::snone'));
        $this->assertTrue(IncompByReference::$declareTable->has('phpmig::sfirst'));
        $this->assertTrue(IncompByReference::$declareTable->has('phpmig::ssecond'));
        $this->assertFalse(IncompByReference::$declareTable->has('phpmig->none'));
        $this->assertTrue(IncompByReference::$declareTable->has('phpmig->first'));
        $this->assertTrue(IncompByReference::$declareTable->has('phpmig->second'));

        $this->assertFalse(IncompByReference::$methodTable->has('->none'));
        $this->assertTrue(IncompByReference::$methodTable->has('->first'));
        $this->assertTrue(IncompByReference::$methodTable->has('->second'));
    }

    public function testPopulateCall()
    {
        // TODO
    }
}
