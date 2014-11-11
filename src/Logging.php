<?php
namespace PhpMigration;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

class Logging
{
    protected static $logger;

    /**
     * Handle dynamic, static calls to the object.
     * inspired by Laravel's Illuminate/Support/Facades/Facade.php
     */
    public static function __callStatic($method, $args)
    {
        if (!isset(static::$logger)) {
            static::$logger = new Logger();
        }
        $logger = static::$logger;

        switch (count($args)) {
            case 0:
                return $logger->$method();

            case 1:
                return $logger->$method($args[0]);

            case 2:
                return $logger->$method($args[0], $args[1]);

            case 3:
                return $logger->$method($args[0], $args[1], $args[2]);

            case 4:
                return $logger->$method($args[0], $args[1], $args[2], $args[3]);

            default:
                return call_user_func_array(array($logger, $method), $args);
        }
    }
}
