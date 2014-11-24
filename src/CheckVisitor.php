<?php
namespace PhpMigration;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt;

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
     * Current class, interface, trait
     */
    protected $class;

    /**
     * Current method in class
     */
    protected $method;

    /**
     * Current function
     */
    protected $function;

    /**
     * Current node
     */
    protected $node;

    /**
     * Empty spots, current state and save the Changes
     */
    public function __construct($changes = array())
    {
        $this->spots = array();
        $this->changes = $changes;
        $this->filename = $this->class = $this->method = $this->function = null;
    }

    /**
     * The interface that allow a Change call another Change's method
     */
    public function callChange($name, $method, $args)
    {
        if (!is_array($args)) {
            $args = array($args);
        }

        foreach ($this->changes as $change) {
            if ($name == substr(get_class($change), -strlen($name))) {
                return call_user_func_array(array($change, $method), $args);
            }
        }
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

    public function getClassname()
    {
        return is_null($this->class) ? null : $this->class->name;
    }

    public function inClass()
    {
        return !is_null($this->class);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function inMethod()
    {
        return !is_null($this->method);
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
        foreach ($this->changes as $change) {
            $change->beforeTraverse();
        }
    }

    public function enterNode(Node $node)
    {
        $this->node = $node;

        // Record current
        if ($node instanceof Stmt\Class_ || $node instanceof Stmt\Interface_ || $node instanceof Stmt\Trait_) {
            /**
             * Class, Interface, Trait are stored in one same HashTable (zend_executor_globals.class_table).
             * Their name will be conflict if duplicated (eg, class Demo {} and Interface Demo {})
             * So, we treat all these class-like's name as Class name.
             */
            $this->class = $node;
        } elseif ($node instanceof Stmt\ClassMethod) {
            $this->method = $node;
        } elseif ($node instanceof Stmt\Function_) {
            $this->function = $node;
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

        // Clear current
        if ($node instanceof Stmt\Class_ || $node instanceof Stmt\Interface_ || $node instanceof Stmt\Trait_) {
            $this->class = null;
        } elseif ($node instanceof Stmt\ClassMethod) {
            $this->method = null;
        } elseif ($node instanceof Stmt\Function_) {
            $this->function = null;
        }

        $this->node = null;
    }

    public function afterTraverse(array $nodes)
    {
        foreach ($this->changes as $change) {
            $change->afterTraverse();
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
    public function addSpot($cate, $certain, $message, $version = '', $line = null, $file = null)
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
        $this->spots[$filename][] = array(
            'cate' => $cate,
            'certain' => $certain,
            'message' => $message,
            'version' => $version,
            'line' => $line,
            'file' => $file,
        );
    }

    /**
     * Get all spots
     */
    public function getSpots()
    {
        return $this->spots;
    }
}
