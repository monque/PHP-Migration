<?php

namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChangeTest;

class IncompMiscTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertNotSpot('never_emit_spot();');

        // array_combine
        $this->assertHasSpot('array_combine();');

        // ob_start
        $this->assertNotSpot('ob_start();');
        $this->assertNotSpot('ob_start(1, 2);');
        $this->assertHasSpot('ob_start(1, 2, 3);');

        // htmlentities, htmlspecialchars
        $this->assertHasSpot('htmlentities();');
        $this->assertHasSpot('htmlspecialchars();');
    }
}
