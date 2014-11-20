<?php
namespace PhpMigration\Changes;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\TestHelper;

abstract class AbstractRemovedTest extends \PHPUnit_Framework_TestCase
{
    protected $change;

    public function testFunc()
    {
        // Not-new
        $code = 'not_removed();';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = sprintf("%s();", $name);
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

            // Case Insensitive name
            $code = sprintf("%s();", strtoupper($name));
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
        }
    }

    public function testConst()
    {
        // Not-new
        $code = 'NOT_REMOVED;';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $table = TestHelper::fetchProperty($this->change, 'constTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = $name.';';
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

            // Case Insensitive name
            $code = strtolower($name).';';
            $this->assertEmpty(TestHelper::runChange($this->change, $code));
        }
    }
}
