<?php

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class ChangesVisitor extends NodeVisitorAbstract
{
    protected $changes;

    protected $filename;

    public function __construct($changes)
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
