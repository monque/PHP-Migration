<?php
namespace PhpMigration\Changes\v5dot3;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractRemovedTest;

class RemovedTest extends AbstractRemovedTest
{
    protected function setUp()
    {
        $this->change = new Removed();
        $this->change->prepare();  // Change must be prepared before test
    }
}
