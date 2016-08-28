<?php
namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

class IncompParamNameTest extends AbstractChangeTest
{
    public function test()
    {
        // Function
        $table = TestHelper::fetchProperty($this->change, 'autoGlobals');
        foreach ($table as $name => $dummy) {
            $this->assertHasSpot(sprintf('function f($%s) {}', $name));
            $this->assertHasSpot(sprintf('class Cl { function f($%s) {} }', $name));

            $this->assertHasSpot(sprintf('function f($a, $b, $c, $%s) {}', $name));
            $this->assertHasSpot(sprintf('class Cl { function f($a, $b, $c, $%s) {} }', $name));
        }

        // Method
        $this->assertHasSpot('class Cl { public function me($this) {} }');
        $this->assertNotSpot('class Cl { public static function me($this) {} }');
    }
}
