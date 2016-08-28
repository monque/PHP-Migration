<?php

namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractChangeTest;

class IncompMagicTest extends AbstractChangeTest
{
    public function testToString()
    {
        $code = 'class Sample{ function __toString(){} }';
        $this->assertNotSpot($code);

        $code = 'class Sample{ function __toString($a){} }';
        $this->assertHasSpot($code);
    }

    public function testNonPub()
    {
        $code = 'class Sample{ function __get(){} }';
        $this->assertNotSpot($code);

        $code = 'class Sample{ public function __get(){} }';
        $this->assertNotSpot($code);

        $code = 'class Sample{ protected function __get($a){} }';
        $this->assertHasSpot($code);

        $code = 'class Sample{ private function __get($a){} }';
        $this->assertHasSpot($code);

        $code = 'class Sample{ public static function __get($a){} }';
        $this->assertHasSpot($code);
    }
}
