<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class StringOperationTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('1 + "0xab";');

        $this->assertHasSpot('1 + "0x01F";');

        $this->assertHasSpot('1 + "0XFFF";');

        $this->assertNotSpot('1 + "0xEOF";');

        $this->assertNotSpot('1 + "24";');
    }
}
