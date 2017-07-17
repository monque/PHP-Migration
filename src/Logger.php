<?php

namespace PhpMigration;

use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;

class Logger extends AbstractLogger
{
    protected $stderr;

    protected static $levels = [
        'emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug',
    ];

    public function log($level, $message, array $context = [])
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
            "[%s] %s\n",
            strtoupper($level),
            $message
        );
    }

    /**
     * Example implementation in http://www.php-fig.org/psr/psr-3/.
     */
    protected function interpolate($message, array $context = [])
    {
        $replace = [];
        foreach ($context as $key => $val) {
            if ($key == 'exception') {
                $val = $val->getMessage();
            }
            $replace['{'.$key.'}'] = $val;
        }

        return strtr($message, $replace);
    }
}
