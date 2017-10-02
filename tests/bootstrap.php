<?php

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->addPsr4('PhpMigration\\', __DIR__);

$oldClass = '\PHPUnit\Framework\TestCase';
$newClass = '\PHPUnit_Framework_TestCase';
if (!class_exists($newClass) && class_exists($oldClass)) {
    class_alias($oldClass, $newClass);
}