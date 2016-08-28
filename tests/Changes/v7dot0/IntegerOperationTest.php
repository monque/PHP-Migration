<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class IntegerOperationTest extends AbstractChangeTest
{
    public function testShift()
    {
        $this->assertHasSpot('1 >> -1;');

        $this->assertNotSpot('1 >> 1;');

        $this->assertHasSpot('$a >> -1;');

        $this->assertNotSpot('$a >> 1;');

        $this->assertHasSpot('1 << -1;');

        $this->assertNotSpot('1 << 1;');

        $this->assertHasSpot('1 << $a;');

        $this->assertNotSpot('1 << 0;');
    }

    public function testModulu()
    {
        $this->assertHasSpot('$a % 0;');

        $this->assertNotSpot('$a % 1;');

        $this->assertHasSpot('1 % 0;');

        $this->assertNotSpot('1 % 1;');

        $this->assertHasSpot('$a % $b;');
    }
}
