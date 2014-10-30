<?php
namespace PhpMigration\Utils;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

class NameHelper
{
    public static function isSameFunc($name, $const)
    {
        if (!is_string($name) && !method_exists($name, '__toString')) {
            return false;
        }

        return strcasecmp($name, $const) === 0;
    }

    public static function isSameClass($name, $const)
    {
        if (!is_string($name) && !method_exists($name, '__toString')) {
            return false;
        }

        return strcasecmp($name, $const) === 0;
    }
}
