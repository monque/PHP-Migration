<?php
namespace PhpMigration\Changes;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Utils\TestHelper;

abstract class AbstractChangeTest extends \PHPUnit_Framework_TestCase
{
    protected $change;

    protected function setUp()
    {
        $chgname = get_class($this);
        if (substr($chgname, -4) == 'Test') {
            $chgname = substr($chgname, 0, -4);
        }
        $this->change = new $chgname;
        $this->change->prepare();
    }

    public function assertHasSpot($code)
    {
        $spots = TestHelper::runChange($this->change, $code);
        $this->assertNotEmpty($spots);
    }

    public function assertNotSpot($code)
    {
        $spots = TestHelper::runChange($this->change, $code);
        $this->assertEmpty($spots);
    }
}
