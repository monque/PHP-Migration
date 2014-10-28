<?php
namespace PhpMigration;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Node;

class Change
{
    /**
     * Visitor who hold this change instance
     */
    protected $visitor;

    /**
     * Assign visitor
     */
    public function setVisitor($visitor)
    {
        $this->visitor = $visitor;
    }

    /**
     * Called before any processing, just after __construct
     */
    public function prepare()
    {
    }

    /**
     * Called after every file have been parsed
     * Usually process data collected in traversing, and return
     */
    public function finish()
    {
    }

    /**
     * Called before Traverser woking
     */
    public function beforeTraverse()
    {
    }

    /**
     * Called after Traverser woking done
     */
    public function afterTraverse()
    {
    }

    /**
     * Called when Traverser enter a node
     */
    public function enterNode(Node $node)
    {
    }

    /**
     * Called when Traverser leave a node
     */
    public function leaveNode(Node $node)
    {
    }
}
