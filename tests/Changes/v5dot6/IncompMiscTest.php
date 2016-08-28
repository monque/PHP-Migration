<?php
namespace PhpMigration\Changes\v5dot6;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

class IncompMiscTest extends AbstractChangeTest
{
    public function testJson()
    {
        $this->assertHasSpot('json_decode();');
    }

    public function testGmp()
    {
        $table = TestHelper::fetchProperty($this->change, 'gmpTable');
        foreach ($table as $name => $dummy) {
            $this->assertHasSpot($name.'();');
        }
    }

    public function testMcrypt()
    {
        $table = TestHelper::fetchProperty($this->change, 'mcryptTable');
        foreach ($table as $name => $dummy) {
            $this->assertHasSpot($name.'();');
        }
    }
}
