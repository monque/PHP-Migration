<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;

class IncompatibleMagicNonPublic extends Change
{
    protected $version = '5.3.0';

    protected $description = <<<EOT
The magic methods __get(), __set(), __isset(), __unset(), and __call() must 
always be public and can no longer be static. Method signatures are now 
enforced.
EOT;

    protected $errmsg = <<<EOT
Warning: The magic method %name% must have public visibility and cannot be static in %file% on line %line%
EOT;

    protected $reference = 'http://php.net/manual/en/migration53.incompatible.php';

    protected $cur_file;

    protected $magics = array(
        '__get',
        '__set',
        '__isset',
        '__unset',
        '__call',
    );

    public function beforeTraverse($filename)
    {
        $this->cur_file = $filename;  // TODO: 统一保存
    }

    public function leaveNode($node)
    {
        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            foreach ($node->getMethods() as $mnode) {
                if (in_array($mnode->name, $this->magics) && !$mnode->isPublic()) {
                    $errmsg = str_replace(
                        array('%name%', '%file%', '%line%'),
                        array($mnode->name, $this->cur_file, $node->getLine()),
                        $this->errmsg
                    );
                    echo $errmsg."\n";
                }
            }
        }
    }
}
