<?php

namespace PhpMigration;

use PhpParser\Node;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitorAbstract;

class CheckVisitor extends NodeVisitorAbstract
{
    /**
     * All Spots emitted by the Changes during check
     */
    protected $spots;

    /**
     * Instances of the Change
     */
    protected $changes;

    /**
     * Current fileinfo
     */
    protected $file;

    /**
     * Current code
     */
    protected $code;

    /**
     * Current node
     */
    protected $node;

    /**
     * Current class-like node (class, interface, trait)
     */
    protected $class;

    /**
     * Stack for record current class-like node
     */
    protected $classStack;

    /**
     * Current funciton-like node (function, method, closure)
     */
    protected $function;

    /**
     * Stack for record current function-like node
     */
    protected $funcStack;

    /**
     * Empty spots, current state and save the Changes
     */
    public function __construct($changes = [])
    {
        $this->spots = [];
        $this->changes = $changes;
        $this->filename = $this->class = $this->method = $this->function = null;
    }

    /**
     * The interface that allow a Change call another Change's method
     */
    public function callChange($name, $method, $args)
    {
        if (!is_array($args)) {
            $args = [$args];
        }

        foreach ($this->changes as $change) {
            if ('PhpMigration\Changes\\'.$name == get_class($change)) {
                return call_user_func_array([$change, $method], $args);
            }
        }
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setFile(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getClassName()
    {
        return is_null($this->class) ? null : $this->class->migName;
    }

    public function inClass()
    {
        return !is_null($this->class);
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function inFunction()
    {
        return !is_null($this->function);
    }

    public function prepare()
    {
        foreach ($this->changes as $change) {
            $change->setVisitor($this);
            $change->prepare();
        }
    }

    public function beforeTraverse(array $nodes)
    {
        $this->classStack = $this->funcStack = [];

        foreach ($this->changes as $change) {
            $change->beforeTraverse($nodes);
        }
    }

    public function enterNode(Node $node)
    {
        $this->node = $node;

        // Record current
        if ($node instanceof Stmt\ClassLike) {
            /**
             * Class, Interface, Trait are stored in one same HashTable
             * (zend_executor_globals.class_table). Their name will be conflict
             * if duplicated (eg, class Demo {} and Interface Demo {}). So, we
             * treat all these class-like's name as Class name.
             */
            $this->class = $node;
            $this->classStack[] = $node;
        } elseif ($node instanceof FunctionLike) {
            $this->function = $node;
            $this->funcStack[] = $node;
        }

        foreach ($this->changes as $change) {
            $change->enterNode($node);
        }

        $this->node = null;
    }

    public function leaveNode(Node $node)
    {
        $this->node = $node;

        foreach ($this->changes as $change) {
            $change->leaveNode($node);
        }

        // Pop current stack
        if ($node instanceof Stmt\ClassLike) {
            $this->class = array_pop($this->classStack);
        } elseif ($node instanceof FunctionLike) {
            $this->function = array_pop($this->funcStack);
        }

        $this->node = null;
    }

    public function afterTraverse(array $nodes)
    {
        foreach ($this->changes as $change) {
            $change->afterTraverse($nodes);
        }
    }

    public function finish()
    {
        foreach ($this->changes as $change) {
            $change->finish();
        }
    }

    /**
     * Add a new spot
     */
    public function addSpot($cate, $identified, $message, $version = '', $line = null, $file = null)
    {
        if (is_null($line) && $this->node instanceof Node) {
            $line = $this->node->getLine();
        }

        if (is_null($file)) {
            $file = $this->getFile();
        }
        if ($file instanceof \SplFileInfo) {
            $filename = $file->getRealpath();
        } else {
            $filename = '';
        }

        // Add by file
        $this->spots[$filename][] = [
            'cate' => $cate,
            'identified' => $identified,
            'message' => $message,
            'version' => $version,
            'line' => $line,
            'file' => $file,
        ];
    }

    /**
     * Get all spots
     */
    public function getSpots()
    {
        return $this->spots;
    }
}
