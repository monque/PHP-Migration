<?php
namespace PhpMigration;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-0, PSR-1, PSR-2 and PSR-4 standards
 * http://www.php-fig.org/
 */

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;

class Logger extends AbstractLogger
{
    protected $stderr;

    protected static $levels = array(
        'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug',
    );

    public function log($level, $message, array $context = array())
    {
        // Check level defined
        if (!in_array($level, static::$levels)) {
            throw new InvalidArgumentException();
        }

        // Verify Exception
        if (isset($context['exception'])) {
            $exception = $context['exception'];
            if (!($exception instanceof \Exception)) {
                $exception = new \Exception((string) $exception);
                $context['exception'] = $exception;
            }
        }

        $message = $this->interpolate($message, $context);

        // Open stream
        if (!isset($this->strerr)) {
            $this->stderr = fopen('php://stderr', 'w');
        }

        fprintf(
            $this->stderr,
            "%s %-10s %s\n",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message
        );
    }

    /*
     * Example implementation in http://www.php-fig.org/psr/psr-3/
     */
    protected function interpolate($message, array $context = array())
    {
        $replace = array();
        foreach ($context as $key => $val) {
            if ($key == 'exception') {
                $val = $val->getMessage();
            }
            $replace['{'.$key.'}'] = $val;
        }

        return strtr($message, $replace);
    }
}
