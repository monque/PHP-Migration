<?php
namespace PhpMigration\Changes;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

abstract class AbstractRemovedTest extends AbstractChangeTest
{
    public function testFunc()
    {
        // Not-new
        $code = 'not_removed();';
        $this->assertNotSpot($code);

        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = sprintf("%s();", $name);
            $this->assertHasSpot($code);

            // Case Insensitive name
            $code = sprintf("%s();", strtoupper($name));
            $this->assertHasSpot($code);

            // Namespaced
            $code = sprintf("use Dummy as %s; %s();", $name, $name);
            $this->assertHasSpot($code);

            $code = sprintf("dummy\%s();", $name, $name);
            $this->assertNotSpot($code);
        }
    }

    public function testConst()
    {
        // Not-new
        $code = 'NOT_REMOVED;';
        $this->assertNotSpot($code);

        $table = TestHelper::fetchProperty($this->change, 'constTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = $name.';';
            $this->assertHasSpot($code);

            // Case Insensitive name
            $code = strtolower($name).';';
            $this->assertNotSpot($code);
        }
    }

    public function testVar()
    {
        // Not-new
        $code = '$not_removed;';
        $this->assertNotSpot($code);

        $table = TestHelper::fetchProperty($this->change, 'varTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            $code = '$'.$name.';';
            $this->assertHasSpot($code);
        }
    }
}
