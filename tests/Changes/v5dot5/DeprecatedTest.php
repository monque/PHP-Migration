<?php

namespace PhpMigration\Changes\v5dot5;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

class DeprecatedTest extends AbstractChangeTest
{
    public function testFunc()
    {
        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        foreach ($table as $name => $dummy) {
            $this->assertHasSpot($name.'();');
        }
    }

    public function testMysqlFunc()
    {
        $table = TestHelper::fetchProperty($this->change, 'mysqlTable');
        foreach ($table as $name => $dummy) {
            $this->assertHasSpot($name.'();');
        }
    }

    public function testPregReplace()
    {
        $this->assertNotSpot('preg_replace();');

        $this->assertNotSpot('preg_replace("//i");');
        $this->assertNotSpot('preg_replace(""."//i");');
        $this->assertNotSpot('preg_replace("/{$part}$pattern/i");');

        $this->assertHasSpot('preg_replace("//e");');
        $this->assertHasSpot('preg_replace(""."//e");');
        $this->assertHasSpot('preg_replace("/{$part}$pattern/e");');
    }
}
