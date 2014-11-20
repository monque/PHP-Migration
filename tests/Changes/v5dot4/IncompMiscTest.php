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

class IncompMiscTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertNotSpot('never_emit_spot();');

        // array_combine
        $this->assertHasSpot('array_combine();');

        // ob_start
        $this->assertNotSpot('ob_start();');
        $this->assertHasSpot('ob_start(1, 2, 3);');

        // htmlentities, htmlspecialchars
        $this->assertHasSpot('htmlentities();');
        $this->assertHasSpot('htmlspecialchars();');
    }
}
