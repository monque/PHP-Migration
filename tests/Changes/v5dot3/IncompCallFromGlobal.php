<?php
namespace PhpMigration\Changes\v5dot3;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\CheckVisitor;
use PhpMigration\TestHelper;

class IncompCallFromGlobalTest extends \PHPUnit_Framework_TestCase
{
    protected $change;

    protected function setUp()
    {
        $this->change = new IncompCallFromGlobal();
        $this->change->prepare();
    }

    public function testBasic()
    {
        $code = 'func_get_arg();';
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

        $code = 'function func() {func_get_arg();}';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $code = 'class Sample {function func() {func_get_arg();}}';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));
    }
}
