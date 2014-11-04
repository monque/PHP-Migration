<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\TestHelper;

class IntroducedTest extends \PHPUnit_Framework_TestCase
{
    protected $change;

    protected function setUp()
    {
        $this->change = new Introduced();
        $this->change->prepare();
    }

    protected function genFuncDef($name)
    {
        $code = sprintf('function %s() {}', $name);
        return TestHelper::getNodeByCode($code);
    }

    public function testNewFunc()
    {
        foreach (Introduced::$funcTable as $name => $dummy) {
            // Normal name
            $node = $this->genFuncDef($name);
            $this->assertTrue($this->change->isNewFunc($node));

            // Messy name
            $messyname = substr($name, 0, -1).strtoupper(substr($name, -1));
            $node = $this->genFuncDef($messyname);
            $this->assertTrue($this->change->isNewFunc($node));
        }

        // Wrong name
        $node = $this->genFuncDef('ohshit');
        $this->assertFalse($this->change->isNewFunc($node));
    }

    protected function genClassDef($name)
    {
        $code = sprintf('class %s {}', $name);
        return TestHelper::getNodeByCode($code);
    }

    public function testNewClass()
    {
        foreach (Introduced::$classTable as $name => $dummy) {
            // Normal name
            $node = $this->genClassDef($name);
            $this->assertTrue($this->change->isNewClass($node));

            // Messy name
            $messyname = substr($name, 0, -1).strtoupper(substr($name, -1));
            $node = $this->genClassDef($messyname);
            $this->assertTrue($this->change->isNewClass($node));
        }

        // Wrong name
        $node = $this->genClassDef('ohshit');
        $this->assertFalse($this->change->isNewFunc($node));
    }

    protected function genConstDef($name)
    {
        $code = sprintf('define("%s", false);', $name);
        return TestHelper::getNodeByCode($code);
    }

    public function testNewConst()
    {
        foreach (Introduced::$constTable as $name => $dummy) {
            // Normal name
            $node = $this->genConstDef($name);
            $this->assertTrue($this->change->isNewConst($node));
        }

        // Wrong name
        $node = $this->genConstDef('ohshit');
        $this->assertFalse($this->change->isNewConst($node));
    }

    protected function genFuncCall($name)
    {
        $code = sprintf('%s();', $name);
        return TestHelper::getNodeByCode($code);
    }

    public function testNewParam()
    {
        foreach (Introduced::$paramTable as $name => $dummy) {
            // Normal name
            $node = $this->genFuncCall($name);
            $this->assertTrue($this->change->isNewParam($node));
        }

        // Wrong name
        $node = $this->genFuncCall('ohshit');
        $this->assertFalse($this->change->isNewParam($node));
    }
}
