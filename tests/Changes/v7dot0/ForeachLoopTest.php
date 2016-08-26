<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class ForeachLoopTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('foreach ($arr as &$i) { current(); }');

        $this->assertHasSpot('foreach ($arr as &$i) { key(); }');

        $this->assertNotSpot('foreach ($arr as &$i) { end(); }');

        $this->assertHasSpot('foreach ($arr as $key => &$i) { key(); }');

        $this->assertNotSpot('foreach ($arr as $key => $i) { key(); }');

        $this->assertNotSpot('foreach ($arr as $key => $i) {}');

        $this->assertHasSpot('foreach ($a as $b) {foreach ($c as &$i) { key(); }}');

        $this->assertHasSpot('foreach ($a as &$b) {foreach ($c as $i) { key(); }}');
    }
}
