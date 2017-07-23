<?php

namespace PhpMigration;

class SymbolTable implements \Iterator, \ArrayAccess
{
    const CS = true;
    const IC = false;

    protected $caseSensitive;

    protected $data;

    public function __construct($data, $case_sensitive = true)
    {
        // Auto flip
        if (is_array($data) && key($data) === 0) {
            $data = array_flip($data);
        }

        $this->data = [];
        foreach ($data as $rawkey => $value) {
            $key = $case_sensitive ? $rawkey : strtolower($rawkey);
            $this->data[$key] = $value;
        }
        $this->caseSensitive = $case_sensitive;
    }

    protected function prepareKey(&$key)
    {
        // Compatible with almost all type key
        if (is_object($key) && method_exists($key, '__toString')) {
            $key = (string) $key;
        }
        if (!is_string($key)) {
            return false;
        }

        $key = $this->caseSensitive ? $key : strtolower($key);

        return true;
    }

    /**
     * Basic operation.
     */
    public function has($key)
    {
        if (!$this->prepareKey($key)) {
            return false;
        }

        return isset($this->data[$key]);
    }

    public function get($key)
    {
        if (!$this->prepareKey($key)) {
            return;
        }

        return isset($this->data[$key]) ? $this->data[$key] : null;
    }

    public function set($key, $value)
    {
        if (!$this->prepareKey($key)) {
            return;
        }

        return $this->data[$key] = $value;
    }

    public function del($key)
    {
        if (!$this->prepareKey($key)) {
            return false;
        }

        if (!isset($this->data[$key])) {
            return false;
        }

        unset($this->data[$key]);

        return true;
    }

    /**
     * Implement Iterator.
     */
    public function current()
    {
        return current($this->data);
    }

    public function key()
    {
        return key($this->data);
    }

    public function next()
    {
        next($this->data);
    }

    public function rewind()
    {
        reset($this->data);
    }

    public function valid()
    {
        return $this->current() !== false;
    }

    /**
     * Implement ArrayAccess.
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->del($offset);
    }
}
