<?php

namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

class IncompHashAlgoTest extends AbstractChangeTest
{
    public function test()
    {
        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        foreach ($table as $name => $dummy) {
            $this->assertHasSpot($name.'("salsa10");');
            $this->assertHasSpot($name.'("salsa20");');
            $this->assertNotSpot($name.'("md5");');

            // Uncertain
            $this->assertHasSpot($name.'($a);');

            // Emtpy
            $this->assertNotSpot($name.'();');
        }
    }
}
