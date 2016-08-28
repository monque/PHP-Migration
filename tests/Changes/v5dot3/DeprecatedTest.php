<?php
namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

class DeprecatedTest extends AbstractChangeTest
{
    public function testFunc()
    {
        // Not-new
        $this->assertNotSpot('not_new();');

        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        foreach ($table as $name => $dummy) {
            // Normal name
            $this->assertHasSpot(sprintf("%s();", $name));

            // Case Insensitive name
            $this->assertHasSpot(sprintf("%s();", strtoupper($name)));
        }
    }

    public function testAssignNewByRef()
    {
        // Direct assign
        $this->assertNotSpot('$o = new Class_();');

        // By-reference
        $this->assertHasSpot('$o = &new Class_();');
    }

    public function testCallTimePassByRef()
    {
        $this->assertNotSpot('func($a, $b, "asdf");');

        // Call-time pass-by-reference
        $this->assertHasSpot('func($a, &$b, "asdf");');

        // Set skip
        $this->change->skipCallTimePassByRef(true);
        $this->assertNotSpot('func($a, &$b, "asdf");');

        // Cancle skip
        $this->change->skipCallTimePassByRef(false);
        $this->assertHasSpot('func($a, &$b, "asdf");');
    }
}
