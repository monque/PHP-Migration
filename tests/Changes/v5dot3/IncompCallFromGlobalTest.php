<?php
namespace PhpMigration\Changes\v5dot3;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractChangeTest;

class IncompCallFromGlobalTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('func_get_arg();');

        $this->assertNotSpot('function func() { func_get_arg(); }');

        $code = 'class Same { function func() { func_get_arg(); } }';
        $this->assertNotSpot($code);
    }
}
