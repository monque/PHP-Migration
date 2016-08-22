<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class FuncListTest extends AbstractChangeTest
{
    public function testVarOrder()
    {
        $this->assertHasSpot('list($a[], $a[], $a[]) = [1, 2, 3];');

        $this->assertNotSpot('list($a, $b) = [1, 2];');
    }

    public function testEmpty()
    {
        $this->assertHasSpot('list() = $a;');

        $this->assertHasSpot('list(,,) = $a;');

        $this->assertHasSpot('list($x, list(), $y) = $a;');

        $this->assertNotSpot('list(, , , , $b) = [1, 2, 2, 4,];');

        $this->assertNotSpot('list($x, list($l), $y) = $a;');
    }
}
