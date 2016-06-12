<?php
namespace PhpMigration\Changes\v7dot0;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractChangeTest;

class IncompVariableTest extends AbstractChangeTest
{
    public function test()
    {
        // http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling
        $this->assertHasSpot('$$foo[\'bar\'][\'baz\'];');
        $this->assertHasSpot('$foo->$bar[\'baz\'];');
        $this->assertHasSpot('$foo->$bar[\'baz\']();');
        $this->assertHasSpot('Foo::$bar[\'baz\']();');

        $this->assertNotSpot('$foo[\'bar\'];');
    }
}
