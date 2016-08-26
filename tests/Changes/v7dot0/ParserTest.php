<?php
namespace PhpMigration\Changes\v7dot0;

use PhpParser\Error;
use PhpParser\ParserFactory;

class ParserTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7);
    }

    /**
     * global only accepts simple variables
     *
     * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.global
     */
    public function testInvalidGlobal()
    {
        $this->expectException(Error::class);

        $this->parser->parse('<?php function f() { global $$foo->bar; }');
    }

    public function testValidGlobal()
    {
        $this->parser->parse('<?php function f() { global ${$foo->bar}; }');
    }

    /**
     * Invalid octal literals
     *
     * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.integers.invalid-octals
     */
    public function testInvalidOctal()
    {
        $this->expectException(Error::class);

        $this->parser->parse('<?php $a = 0128;');
    }

    public function testValidOctal()
    {
        $this->parser->parse('<?php $a = 0127;');
    }
}
