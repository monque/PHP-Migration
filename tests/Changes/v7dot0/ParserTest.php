<?php

namespace PhpMigration\Changes\v7dot0;

use PhpParser\Error;
use PhpParser\ParserFactory;

class ParserTest extends \PHPUnit\Framework\TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
    }

    /**
     * global only accepts simple variables
     *
     * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.global
     * @expectedException PhpParser\Error
     */
    public function testInvalidGlobal()
    {
        // BC for PHPUnit 4.8
        if (method_exists($this, 'expectException')) {
            $this->expectException(Error::class);
        }

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
     * @expectedException PhpParser\Error
     */
    public function testInvalidOctal()
    {
        // BC for PHPUnit 4.8
        if (method_exists($this, 'expectException')) {
            $this->expectException(Error::class);
        }

        $this->parser->parse('<?php $a = 0128;');
    }

    public function testValidOctal()
    {
        $this->parser->parse('<?php $a = 0127;');
    }
}
