<?php
namespace PhpMigration\Changes\v5dot3;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\TestHelper;

class DeprecatedTest extends \PHPUnit_Framework_TestCase
{
    protected $change;

    protected function setUp()
    {
        $this->change = new Deprecated();
        $this->change->prepare();
    }

    public function testFunc()
    {
        // Not-new
        $code = 'not_new();';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = sprintf("%s();", $name);
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

            // Case Insensitive name
            $code = sprintf("%s();", strtoupper($name));
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
        }

        // Set skip mysql func
        $code = 'mysql_escape_string();';
        $this->change->skipMysqlFunc(true); // Skip
        $this->assertEmpty(TestHelper::runChange($this->change, $code));
        $this->change->skipMysqlFunc(false); // Dont skip
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
    }

    public function testAssignNewByRef()
    {
        // Direct assign
        $code = '$o = new Class_();';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        // By-reference
        $code = '$o = &new Class_();';
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
    }

    public function testCallTimePassByRef()
    {
        $code = 'func($a, $b, "asdf");';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        // By-reference
        $code = 'func($a, &$b, "asdf");';
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
    }
}
