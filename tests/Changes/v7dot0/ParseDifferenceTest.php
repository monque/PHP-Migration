<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class ParseDifferenceTest extends AbstractChangeTest
{
    /**
     * Changes to the handling of indirect variables, properties, and methods
     *
     * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.indirect
     */
    public function testPrecedence()
    {
        $this->assertHasSpot('$$foo[\'bar\'][\'baz\'];');
        $this->assertHasSpot('$foo->$bar[\'baz\'];');
        $this->assertHasSpot('$foo->$bar[\'baz\']();');
        $this->assertHasSpot('Foo::$bar[\'baz\']();');

        $this->assertNotSpot('$foo[\'bar\'];');
    }

    /**
     * yield is now a right associative operator
     *
     * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.other.yield
     */
    public function testYield()
    {
        $this->assertHasSpot('echo yield -1;');

        $this->assertHasSpot('yield $foo or die;');
    }
}
