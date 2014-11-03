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

class DeprecatedTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;

    protected $change;

    protected function setUp()
    {
        $this->parser = new Parser(new Lexer\Emulative);

        $this->change = new Deprecated();
        $this->change->prepare();
    }

    protected function genFuncCall($name)
    {
        $code = sprintf('<?php %s();', $name);
        $stmts = $this->parser->parse($code);
        return $stmts[0];
    }

    public function testDeprecatedFunc()
    {
        foreach (Deprecated::$funcTable as $name => $dummy) {
            // Normal name
            $node = $this->genFuncCall($name);
            $this->assertTrue($this->change->isDeprecatedFunc($node));

            // Messy name
            $messyname = substr($name, 0, -1).strtoupper(substr($name, -1));
            $node = $this->genFuncCall($messyname);
            $this->assertTrue($this->change->isDeprecatedFunc($node));
        }

        // Indeprecated name
        $node = $this->genFuncCall('ohshit');
        $this->assertFalse($this->change->isDeprecatedFunc($node));
    }

    public function testAssignNewByRef()
    {
        // Direct assign
        $node = current($this->parser->parse('<?php $o = new Class_();'));
        $this->assertFalse($this->change->isAssignNewByRef($node));

        // By-reference
        $node = current($this->parser->parse('<?php $o = &new Class_();'));
        $this->assertTrue($this->change->isAssignNewByRef($node));
    }
}
