<?php
namespace PhpMigration\Changes;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractChangeTest;
use PhpMigration\Utils\TestHelper;

abstract class AbstractRemovedTest extends AbstractChangeTest
{
    public function testFunc()
    {
        // Not-new
        $code = 'not_removed();';
        $this->assertNotSpot($code);

        $table = TestHelper::fetchProperty($this->change, 'funcTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = sprintf("%s();", $name);
            $this->assertHasSpot($code);

            // Case Insensitive name
            $code = sprintf("%s();", strtoupper($name));
            $this->assertHasSpot($code);
        }
    }

    public function testConst()
    {
        // Not-new
        $code = 'NOT_REMOVED;';
        $this->assertNotSpot($code);

        $table = TestHelper::fetchProperty($this->change, 'constTable');
        if (is_null($table)) {
            return;
        }
        foreach ($table as $name => $dummy) {
            // Normal name
            $code = $name.';';
            $this->assertHasSpot($code);

            // Case Insensitive name
            $code = strtolower($name).';';
            $this->assertNotSpot($code);
        }
    }
}
