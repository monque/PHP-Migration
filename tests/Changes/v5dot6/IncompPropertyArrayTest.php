<?php

namespace PhpMigration\Changes\v5dot6;

use PhpMigration\Changes\AbstractChangeTest;

class IncompPropertyArrayTest extends AbstractChangeTest
{
    public function test()
    {
        // Empty class
        $this->assertNotSpot('class Sample { }');

        // Without default value, non-array type
        $this->assertNotSpot('class Sample { public $pro; protected $int = 5; private $str = "he";}');

        // Empty array
        $this->assertNotSpot('class Sample { public $arr = array(); }');

        // Class-const-key with null
        $code = <<<'EOC'
class Sample
{
    const CK = 'one';
    public $data = array(
        self::CK => 1,
        2,
    );
}
EOC;
        $this->assertHasSpot($code);

        // Class-const-key unfetched
        $code = <<<'EOC'
class Sample
{
    public $data = array(
        self::CK    => 1,
        'two'       => 2,
    );
}
EOC;
        $this->assertHasSpot($code);

        // Class-const-key fetched, but duplicated
        $code = <<<'EOC'
class Sample
{
    const CK = 'one';
    public $data = array(
        self::CK    => 1,
        'one'       => 2,
    );
}
EOC;
        $this->assertHasSpot($code);

        // Global-const-key is always unfetched
        $code = <<<'EOC'
class Sample
{
    public $data = array(
        CK => 1,
    );
}
EOC;
        $this->assertHasSpot($code);

        // Class-const-key duplicated
        $code = <<<'EOC'
class Sample
{
    const CK = 'one';
    public $data = array(
        self::CK => 1,
        self::CK => 1,
    );
}
EOC;
        $this->assertHasSpot($code);

        // Exception, all const feteched, and no duplicated
        $code = <<<'EOC'
class Sample
{
    const CK1 = 'one';
    const CK2 = 'two';
    public $data = array(
        self::CK1   => 1,
        self::CK2   => 1,
        'third'     => 0,
    );
}
EOC;
        $this->assertNotSpot($code);
    }
}
