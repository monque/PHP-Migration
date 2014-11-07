<?php
namespace PhpMigration\Changes;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\TestHelper;

class AbstractIntroducedTest extends \PHPUnit_Framework_TestCase
{
    protected $change;

    public function testNewFunc()
    {
        // Not-new
        $code = 'function not_new() {}';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $table = TestHelper::getProperty($this->change, 'funcTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = sprintf("function %s() {}", $name);
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

            // Case Insensitive name
            $code = sprintf("function %s() {}", strtoupper($name));
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

            // Conditional name
            $code = sprintf("if (!function_exists('%s')) { function %s() {} }", $name, $name);
            $this->assertEmpty(TestHelper::runChange($this->change, $code));
        }

        // Error-conditional name
        $code = sprintf("if (!function_exists('nothing')) { function %s() {} }", $name);
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
    }

    public function testNewClass()
    {
        // Not-new
        $code = 'class not_new {}';
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $table = TestHelper::getProperty($this->change, 'classTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = sprintf("class %s {}", $name);
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

            // Case Insensitive name
            $code = sprintf("class %s {}", strtoupper($name));
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

            // Conditional name
            $code = sprintf("if (!class_exists('%s')) { class %s {} }", $name, $name);
            $this->assertEmpty(TestHelper::runChange($this->change, $code));
        }

        // Error-conditional name
        $code = sprintf("if (!class_exists('nothing')) { class %s {} }", $name);
        $this->assertNotEmpty(TestHelper::runChange($this->change, $code));
    }

    protected function genDefine($name)
    {
        return 'define("'.$name.'", 0);';
    }

    public function testNewConst()
    {
        // Not-new
        $code = $this->genDefine('NOTNEW');
        $this->assertEmpty(TestHelper::runChange($this->change, $code));

        $table = TestHelper::getProperty($this->change, 'constTable');
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = $this->genDefine($name);
            $this->assertNotEmpty(TestHelper::runChange($this->change, $code));

            // Case Insensitive name
            $code = $this->genDefine(strtolower($name));
            $this->assertEmpty(TestHelper::runChange($this->change, $code));
        }
    }
}
