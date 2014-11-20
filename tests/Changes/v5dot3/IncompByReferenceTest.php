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

class IncompByReferenceTest extends \PHPUnit_Framework_TestCase
{
    protected $change;

    protected function setUp()
    {
        $this->change = new IncompByReference();
        $this->change->prepare();
    }

    public function testFunc()
    {
        $code = 'function sample($a) {} sample();';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $code = 'function sample($a, $b, $c, $d) {} sample(1, 2, 3, 4);';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $code = 'function sample($a, $b, &$c, $d) {} sample(1, 2, 3, 4);';
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

        // Case Insensitive
        $code = 'function sample($a, $b, &$c, $d) {} saMPLe(1, 2, 3, 4);';
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

        // Built-in
        $table = TestHelper::fetchProperty($this->change, 'builtinTable');
        foreach ($table as $name => $dummy) {
            $code = sprintf('%s($a, $b);', $name);
            $this->assertEmpty(TestHelper::runChange($this->change, $code));

            $code = sprintf('%s(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);', $name);
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
        }
    }

    public function testMehtod()
    {
        $code = 'class Sample {static function sample($a) {}} Sample::sample();';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $code = 'class Sample {static function sample($a, $b, $c, $d) {}} Sample::sample(1, 2, 3, 4);';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $code = 'class Sample {static function sample($a, $b, &$c, $d) {}} Sample::sample(1, 2, 3, 4);';
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

        $code = 'class Sample {static function sample($a, $b, &$c, $d) {}} sAMPLE::saMPLe(1, 2, 3, 4);';
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
    }
}
