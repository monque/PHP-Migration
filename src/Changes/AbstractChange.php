<?php

namespace PhpMigration\Changes;

abstract class AbstractChange
{
    /**
     * Version represents when this change do perform
     */
    protected static $version;

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
     * Quick method to add spot to visitor
     */
    public function addSpot($cate, $identified, $message, $line = null, $file = null)
    {
        $this->visitor->addSpot(
            $cate,
            $identified,
            $message,
            static::$version,
            $line,
            $file
        );
    }

    /**
     * Initialization of properties
     */
    public function __construct()
    {
    }

    /**
     * Called before any processing, after __construct()
     */
    public function prepare()
    {
    }

    /**
     * Called after all file have been parsed
     * Usually process data collected in traversing, and return
     */
    public function finish()
    {
    }

    /**
     * De-initialization of properties, after finish()
     */
    public function __destruct()
    {
    }

    /**
     * Called before Traverser woking
     */
    public function beforeTraverse(array $nodes)
    {
    }

    /**
     * Called after Traverser woking done
     */
    public function afterTraverse(array $nodes)
    {
    }

    /**
     * Called when Traverser enter a node
     */
    public function enterNode($node)
    {
    }

    /**
     * Called when Traverser leave a node
     */
    public function leaveNode($node)
    {
    }
}
