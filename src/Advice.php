<?php
namespace PhpMigration;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-0, PSR-1, PSR-2 and PSR-4 standards
 * http://www.php-fig.org/
 */

class Advice
{
    protected $file;

    protected $line;

    protected $description;

    protected $reference;

    public function __construct($file, $line, $data = null)
    {
    }
}
