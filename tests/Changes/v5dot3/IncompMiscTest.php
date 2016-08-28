<?php
namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

class IncompMiscTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertNotSpot('never_emit_spot();');

        $this->assertHasSpot('clearstatcache();');

        $this->assertHasSpot('realpath();');

        $this->assertHasSpot('gd_info();');

        $table = TestHelper::fetchProperty($this->change, 'arrFuncTable');
        foreach ($table as $name => $dummy) {
            $this->assertHasSpot($name.'();');
        }
    }

    public function testCallFunc()
    {
        $this->assertNotSpot('call_user_func_array();');

        $this->assertNotSpot('call_user_func_array($a, array());');

        $this->assertHasSpot('call_user_func_array($a, $b);');

        $this->assertHasSpot('call_user_func_array($a, null);');

        $this->assertHasSpot('call_user_func_array($a, "str");');

        $this->assertHasSpot('call_user_func_array($a, 123);');
    }
}
