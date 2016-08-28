<?php
namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChangeTest;

class DeprecatedTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('mcrypt_generic_end();');
        $this->assertHasSpot('mysql_list_dbs();');
    }
}
