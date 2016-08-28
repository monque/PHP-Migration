<?php

namespace PhpMigration\Changes;

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

abstract class AbstractIntroducedTest extends AbstractChangeTest
{
    public function testNewFunc()
    {
        // Not-new
        $code = 'function not_new() {}';
        $this->assertNotSpot($code);

        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = sprintf("function %s() {}", $name);
            $this->assertHasSpot($code);

            // Case Insensitive name
            $code = sprintf("function %s() {}", strtoupper($name));
            $this->assertHasSpot($code);

            // Namespaced
            $code = sprintf("namespace Dummy; function %s() {}", $name);
            $this->assertNotSpot($code);

            // Conditional name
            $code = sprintf("if (!function_exists('%s')) { function %s() {} }", $name, $name);
            $this->assertNotSpot($code);
        }

        // Error-conditional name
        $code = sprintf("if (!function_exists('nothing')) { function %s() {} }", $name);
        $this->assertHasSpot($code);
    }

    protected function genMethod($class, $method)
    {
        return sprintf('class Sample extends %s { public function %s() {} }', $class, $method);
    }

    public function testNewMethod()
    {
        $table = TestHelper::fetchProperty($this->change, 'methodTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            list($class, $method) = explode('::', $name);

            // Normal name
            $code = $this->genMethod($class, $method);
            $this->assertHasSpot($code);

            // Case Insensitive name
            $code = $this->genMethod(strtoupper($class), strtoupper($method));
            $this->assertHasSpot($code);

            // Namespaced
            $code = 'namespace Dummy; '.$this->genMethod($class, $method);
            $this->assertNotSpot($code);
        }
    }

    public function testNewClass()
    {
        // Not-new
        $code = 'class not_new {}';
        $this->assertNotSpot($code);

        $table = TestHelper::fetchProperty($this->change, 'classTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = sprintf("class %s {}", $name);
            $this->assertHasSpot($code);

            // Case Insensitive name
            $code = sprintf("class %s {}", strtoupper($name));
            $this->assertHasSpot($code);

            // Namespaced
            $code = sprintf("namespace Dummy; class %s {}", $name);
            $this->assertNotSpot($code);

            // Conditional name
            // Removed, because of autoload it's too rare to see
            // $code = sprintf("if (!class_exists('%s')) { class %s {} }", $name, $name);
            // $this->assertNotSpot($code);
        }
    }

    protected function genDefine($name)
    {
        return 'define("'.$name.'", 0);';
    }

    public function testNewConst()
    {
        // Not-new
        $code = $this->genDefine('NOTNEW');
        $this->assertNotSpot($code);

        $table = TestHelper::fetchProperty($this->change, 'constTable');
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = $this->genDefine($name);
            $this->assertHasSpot($code);

            // Case Insensitive name
            $code = $this->genDefine(strtolower($name));
            $this->assertNotSpot($code);
        }

        // #Issue 7: First argument not a string
        $this->assertNotSpot('define(DUMMY, 0);');
        $this->assertNotSpot('define(123, 0);');
        $this->assertNotSpot('define($dummy, 0);');
    }
}
