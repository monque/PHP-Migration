<?php
namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChangeTest;

class IncompRegisterTest extends AbstractChangeTest
{
    public function test()
    {
        $this->assertHasSpot('$HTTP_POST_VARS;');
        $this->assertHasSpot('$HTTP_GET_VARS;');
        $this->assertHasSpot('$HTTP_ENV_VARS;');
        $this->assertHasSpot('$HTTP_SERVER_VARS;');
        $this->assertHasSpot('$HTTP_COOKIE_VARS;');
        $this->assertHasSpot('$HTTP_SESSION_VARS;');
        $this->assertHasSpot('$HTTP_POST_FILES;');

        // Case error
        $this->assertNotSpot('$HttP_POST_VARS;');
        $this->assertNotSpot('$HttP_GET_VARS;');
        $this->assertNotSpot('$HttP_ENV_VARS;');
        $this->assertNotSpot('$HttP_SERVER_VARS;');
        $this->assertNotSpot('$HttP_COOKIE_VARS;');
        $this->assertNotSpot('$HttP_SESSION_VARS;');
        $this->assertNotSpot('$HttP_POST_FILES;');

        $this->assertNotSpot('$HTTP_SHIT_VARS;');

        $this->assertNotSpot('$$name;');
    }
}
