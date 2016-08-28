<?php
namespace PhpMigration\Changes\v5dot5;

use PhpMigration\Changes\AbstractChangeTest;

class IncompPackTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertNotSpot('unpack();');
        $this->assertNotSpot('unpack("x");');

        $this->assertHasSpot('unpack("a");');
        $this->assertHasSpot('unpack("A");');
        $this->assertHasSpot('unpack($a);');
        $this->assertHasSpot('unpack($a."");');
        $this->assertHasSpot('unpack("$b");');
    }
}
