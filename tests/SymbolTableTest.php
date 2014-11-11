<?php
namespace PhpMigration;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

class SymbolTableTest extends \PHPUnit_Framework_TestCase
{
    protected $fillData = array(
        'CamelCase' => 'CamelCase',
    );

    protected $csEmptyTable;

    protected $icEmptyTable;

    protected $csFilledTable;

    protected $icFilledTable;

    protected function setUp()
    {
        $this->csEmptyTable = new SymbolTable(array(), SymbolTable::CS);
        $this->icEmptyTable = new SymbolTable(array(), SymbolTable::IC);
        $this->csFilledTable = new SymbolTable($this->fillData, SymbolTable::CS);
        $this->icFilledTable = new SymbolTable($this->fillData, SymbolTable::IC);
    }

    public function testHas()
    {
        // Varied param type
        $this->assertFalse($this->csFilledTable->has(false));
        $this->assertFalse($this->csFilledTable->has(null));
        $this->assertFalse($this->csFilledTable->has(1234));
        $this->assertFalse($this->csFilledTable->has(12.34));
        $this->assertFalse($this->csFilledTable->has(new \DateTime()));

        // Case Sensitive
        $this->assertTrue($this->csFilledTable->has('CamelCase'));
        $this->assertFalse($this->csFilledTable->has('camelcase'));

        // Case Insensitive
        $this->assertTrue($this->icFilledTable->has('CamelCase'));
        $this->assertTrue($this->icFilledTable->has('camelcase'));
    }


    public function testGet()
    {
        // Case Sensitive
        $this->assertEquals('CamelCase', $this->csFilledTable->get('CamelCase'));
        $this->assertEquals(null, $this->csFilledTable->get('cAMELcASE'));

        // Case Insensitive
        $this->assertEquals('CamelCase', $this->icFilledTable->get('CamelCase'));
        $this->assertEquals('CamelCase', $this->icFilledTable->get('cAMELcASE'));
    }

    public function testSet()
    {
        // Case Sensitive
        $this->csEmptyTable->set('KEY', 'Upper key');
        $this->assertTrue($this->csEmptyTable->has('KEY'));
        $this->assertFalse($this->csEmptyTable->has('key'));
        $this->assertEquals('Upper key', $this->csEmptyTable->get('KEY'));
        $this->assertEquals(null, $this->csEmptyTable->get('key'));

        // Case Insensitive
        $this->icEmptyTable->set('KEY', 'Upper key');
        $this->assertTrue($this->icEmptyTable->has('KEY'));
        $this->assertTrue($this->icEmptyTable->has('key'));
        $this->assertEquals('Upper key', $this->icEmptyTable->get('KEY'));
        $this->assertEquals('Upper key', $this->icEmptyTable->get('key'));
    }
}
