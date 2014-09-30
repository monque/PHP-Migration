<?php

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Node;

class Change
{
    /**                                                                        
     * The version this change been introduced
     */ 
    protected $version;

    /**                                                                        
     * The description writen in the official migration guide on php.net
     */ 
    protected $description;

    /**                                                                        
     * Message that will be outputed by PHP
     */ 
    protected $errmsg;

    /**                                                                        
     * The link to the official guide
     */ 
    protected $reference;

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
    public function beforeTraverse($filename)
    {
    }

    /**
     * Called after Traverser woking done
     */
    public function afterTraverse($filename)
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
