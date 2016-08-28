<?php

namespace PhpMigration\Changes\v5dot6;

use PhpMigration\Changes\AbstractChangeTest;

class DeprecatedTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('$HTTP_RAW_POST_DATA;');
    }
}
