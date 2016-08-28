<?php

namespace PhpMigration\Changes\v5dot5;

use PhpMigration\Changes\AbstractChangeTest;

class IncompCaseInsensitiveTest extends AbstractChangeTest
{
    public function test()
    {
        // Call
        $this->assertNotSpot('self::call();');
        $this->assertNotSpot('parent::call();');
        $this->assertNotSpot('static::call();');

        $this->assertHasSpot('sElf::call();');
        $this->assertHasSpot('pArent::call();');
        $this->assertHasSpot('sTatic::call();');

        // Property
        $this->assertNotSpot('self::$fetch;');
        $this->assertNotSpot('parent::$fetch;');
        $this->assertNotSpot('static::$fetch;');

        $this->assertHasSpot('sElf::$fetch;');
        $this->assertHasSpot('pArent::$fetch;');
        $this->assertHasSpot('sTatic::$fetch;');
    }
}
