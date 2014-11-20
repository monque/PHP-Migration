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

class IncompRegisterTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('$HTTP_SHIT_VARS;');

        $this->assertNotSpot('$http_SHIT_VARS;');

        $this->assertNotSpot('$$name;');
    }
}
