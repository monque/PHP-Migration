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
