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

class DeprecatedTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('mcrypt_generic_end();');
        $this->assertHasSpot('mysql_list_dbs();');
    }
}
