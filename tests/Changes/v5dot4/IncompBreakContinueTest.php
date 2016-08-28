<?php
namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChangeTest;

class IncompBreakContinueTest extends AbstractChangeTest
{
    public function test()
    {
        // Break
        $this->assertNotSpot('break;');
        $this->assertNotSpot('break 1;');
        $this->assertNotSpot('break 100;');

        $this->assertHasSpot('break $a;');
        $this->assertHasSpot('break 0;');
        $this->assertHasSpot('break 1 + 1;');

        // Continue
        $this->assertNotSpot('continue;');
        $this->assertNotSpot('continue 1;');
        $this->assertNotSpot('continue 100;');

        $this->assertHasSpot('continue $a;');
        $this->assertHasSpot('continue 0;');
        $this->assertHasSpot('continue 1 + 1;');
    }
}
