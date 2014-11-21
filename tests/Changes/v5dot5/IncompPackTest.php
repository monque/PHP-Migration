<?php
namespace PhpMigration\Changes\v5dot5;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractChangeTest;

class IncompPackTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertNotSpot('unpack();');
        $this->assertNotSpot('unpack("x");');

        $this->assertHasSpot('unpack("a");');
        $this->assertHasSpot('unpack("A");');
        $this->assertHasSpot('unpack($a);');
    }
}
