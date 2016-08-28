<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

class KeywordReservedTest extends AbstractChangeTest
{
    public function test()
    {
        $words = [];

        $table = TestHelper::fetchProperty($this->change, 'forbiddenTable');
        foreach ($table as $name => $dummy) {
            $words[] = $name;
        }

        $table = TestHelper::fetchProperty($this->change, 'reservedTable');
        foreach ($table as $name => $dummy) {
            $words[] = $name;
        }

        $this->assertNotSpot('class not_keyword {}');

        $this->assertNotSpot('trait not_keyword {}');

        $this->assertNotSpot('interface not_keyword {}');

        $this->assertNotSpot('$o = new class {};');

        foreach ($words as $word) {
            $this->assertHasSpot('class '.$word.' {}');

            $this->assertHasSpot('trait '.$word.' {}');

            $this->assertHasSpot('interface '.$word.' {}');

            $this->assertHasSpot('namespace Dummy; class '.$word.' {}');

            $this->assertHasSpot('namespace Dummy; trait '.$word.' {}');

            $this->assertHasSpot('namespace Dummy; interface '.$word.' {}');
        }
    }
}
