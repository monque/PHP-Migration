<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class FuncParametersTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('function f($a, $a) {}');

        $this->assertHasSpot('class C { function f($a, $a) {} }');

        $this->assertHasSpot('$a = function ($a, $a) {};');

        $this->assertHasSpot('function f($a, $b, $a) {}');

        $this->assertHasSpot('class C { function f($a, $b, $a) {} }');

        $this->assertHasSpot('$a = function ($a, $b, $a) {};');

        $this->assertNotSpot('function f() {}');

        $this->assertNotSpot('function f($a, $b) {}');
    }
}
