<?php
namespace PhpMigration;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-0, PSR-1, PSR-2 and PSR-4 standards
 * http://www.php-fig.org/
 */

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ChangesVisitor extends NodeVisitorAbstract
{
    protected $changes;

    protected $filename;

    public function __construct($changes = array())
    {
        $this->changes = $changes;
        $this->filename = null;
    }

    public function setFilename($name)
    {
        $this->filename = $name;
    }

    public function prepare()
    {
        foreach ($this->changes as $change) {
            $change->prepare();
        }
    }

    public function beforeTraverse(array $nodes)
    {
        foreach ($this->changes as $change) {
            $change->beforeTraverse($this->filename);
        }
    }

    public function enterNode(Node $node)
    {
        foreach ($this->changes as $change) {
            $change->enterNode($node);
        }
    }

    public function leaveNode(Node $node)
    {
        foreach ($this->changes as $change) {
            $change->leaveNode($node);
        }
    }

    public function afterTraverse(array $nodes)
    {
        foreach ($this->changes as $change) {
            $change->afterTraverse($this->filename);
        }
    }

    public function finish()
    {
        foreach ($this->changes as $change) {
            $change->finish();
        }
    }
}
