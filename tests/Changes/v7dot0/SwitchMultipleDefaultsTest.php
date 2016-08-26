<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class SwitchMultipleDefaultsTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('switch ($a) { default: default: }');

        $this->assertNotSpot('switch ($a) { case 1: default: }');

        $this->assertHasSpot('switch ($a) { default: case 2: default: }');

        $this->assertHasSpot('switch ($a) { default: break; default: break; }');
    }
}
