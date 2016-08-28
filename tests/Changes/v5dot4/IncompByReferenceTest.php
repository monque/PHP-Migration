<?php
namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChangeTest;

class IncompByReferenceTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertNotSpot('func();');
        $this->assertNotSpot('func(1);');
        $this->assertNotSpot('func($a);');

        $this->assertHasSpot('func(&$a);');
        $this->assertHasSpot('$obj->func(&$a);');
        $this->assertHasSpot('Sample::func(&$a);');
    }
}
