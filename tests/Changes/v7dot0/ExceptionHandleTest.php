<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class ExceptionHandleTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertNotSpot('set_exception_handler();');

        $this->assertHasSpot('set_exception_handler($handler);');

        $this->assertHasSpot('set_exception_handler([$this, "handler"]);');

        $this->assertNotSpot('set_exception_handler(function () {});');

        $this->assertNotSpot('set_exception_handler(function ($e) {});');

        $this->assertHasSpot('set_exception_handler(function (Exception $e) {});');

        $this->assertHasSpot('set_exception_handler(function (\Exception $e) {});');

        $this->assertHasSpot('use Dummy\Exception; set_exception_handler(function (Exception $e) {});');
    }
}
