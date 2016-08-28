<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChangeTest;

class DeprecatedTest extends AbstractChangeTest
{
    public function testOldConstructor()
    {
        $this->assertHasSpot('class OldClass { function OldClass() {} }');

        $this->assertHasSpot('namespace Dummy; class OldClass { function OldClass() {} }');

        $this->assertNotSpot('class OldClass { function setName() {} }');

        $this->assertNotSpot('class OldClass { function _construct() {} }');
    }

    public function testPasswordHash()
    {
        $this->assertHasSpot('password_hash($a, $b, $c);');

        $this->assertHasSpot('password_hash($a, $b, []);');

        $this->assertNotSpot('password_hash($a, $b);');

        $this->assertNotSpot('Dummy\password_hash($a, $b, []);');
    }

    public function testLdapSort()
    {
        $this->assertHasSpot('ldap_sort();');

        $this->assertNotSpot('Dummy\ldap_sort();');
    }
}
