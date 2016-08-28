<?php

namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChangeTest;

class IncompCallFromGlobalTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('func_get_arg();');

        $this->assertNotSpot('function func() { func_get_arg(); }');

        $code = 'class Same { function func() { func_get_arg(); } }';
        $this->assertNotSpot($code);
    }
}
