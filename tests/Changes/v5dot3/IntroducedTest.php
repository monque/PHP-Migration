<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Lexer;
use PhpParser\Parser;

class IntroducedTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;

    protected $change;

    protected function setUp()
    {
        $this->parser = new Parser(new Lexer\Emulative);

        $this->change = new Introduced();
        $this->change->prepare();
    }

    protected function genFuncDef($name)
    {
        // TODO: merge to utility
        $code = sprintf('<?php function %s() {}', $name);
        $stmts = $this->parser->parse($code);
        return $stmts[0];
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
        $code = sprintf('<?php class %s {}', $name);
        $stmts = $this->parser->parse($code);
        return $stmts[0];
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
        $code = sprintf('<?php define("%s", false);', $name);
        $stmts = $this->parser->parse($code);
        return $stmts[0];
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
        $code = sprintf('<?php %s();', $name);
        $stmts = $this->parser->parse($code);
        return $stmts[0];
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
