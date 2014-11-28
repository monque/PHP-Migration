<?php
namespace PhpMigration\Changes\v5dot4;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

class IncompHashAlgoTest extends AbstractChangeTest
{
    public function test()
    {
        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        foreach ($table as $name => $dummy) {
            $this->assertHasSpot($name.'("salsa10");');
            $this->assertHasSpot($name.'("salsa20");');
            $this->assertNotSpot($name.'("md5");');

            // Uncertain
            $this->assertHasSpot($name.'($a);');

            // Emtpy
            $this->assertNotSpot($name.'();');
        }
    }
}

