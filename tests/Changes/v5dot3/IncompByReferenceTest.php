<?php

namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChangeTest;

class IncompByReferenceTest extends AbstractChangeTest
{
    public function testFunc()
    {
        $this->assertNotSpot('function sample() {} sample();');

        $this->assertNotSpot('function sample() {} sample(1);');

        $this->assertNotSpot('function sample($a, $b) {} sample(1, 2);');

        $this->assertNotSpot('function sample($a, &$b) {} sample(1, $a);');

        $this->assertHasSpot('function sample($a, &$b) {} sample(1, 2);');

        $this->assertHasSpot('function sample($a, &$b) {} sample(1, "h");');

        $this->assertHasSpot('function sample($a, &$b) {} sample(1, array(1));');

        $this->assertHasSpot('function sample($a, &$b) {} sample(1, true);');

        // Case Insensitive
        $this->assertHasSpot('function SAMple($a, &$b) {} samPLE(1, "h");');

        // Built-in
        $this->assertNotSpot('array_shift($a);');
        $this->assertNotSpot('ksort($a);');

        $this->assertHasSpot('array_shift(1);');
        $this->assertHasSpot('ksort(1);');
    }

    public function testMehtod()
    {
        $code = 'class Sample {static function sample($a) {}} Sample::sample();';
        $this->assertNotSpot($code);

        $code = 'class Sample {static function sample($a, $b) {}} Sample::sample(1, 2);';
        $this->assertNotSpot($code);

        $code = 'class Sample {static function sample($a, &$b) {}} Sample::sample(1, 2);';
        $this->assertHasSpot($code);

        $code = 'class Sample {static function sample($a, &$b) {}} sAMPLE::saMPLe(1, 2);';
        $this->assertHasSpot($code);

        // #Issue 13: Call to undefined method PhpParser\Node\Expr\ArrayDimFetch::toString()
        $code = '$cb[0]::sample(1);';
        $this->assertNotSpot($code);
    }
}
