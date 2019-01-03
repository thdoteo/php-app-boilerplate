<?php

namespace Framework\Database;

class QueryResult implements \ArrayAccess, \Iterator
{
    /**
     * @var array
     */
    private $results;

    /**
     * @var int
     */
    private $index = 0;

    /**
     * @var array
     */
    private $hydratedResults = [];

    /**
     * @var string
     */
    private $entity;

    public function __construct(array $results, ?string $entity = null)
    {
        $this->results = $results;
        $this->entity = $entity;
    }

    public function get(int $index)
    {
        if ($this->entity) {
            if (!isset($this->hydratedResults[$index])) {
                $this->hydratedResults[$index] = Hydrator::hydrate($this->results[$index], $this->entity);
            }
            return $this->hydratedResults[$index];
        }
        return $this->all()[$index];
    }

    /**
     * Return the current element
     */
    public function current()
    {
        return $this->get($this->index);
    }

    /**
     * Move forward to next element
     */
    public function next(): void
    {
        $this->index++;
    }

    /**
     * Return the key of the current element
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     */
    public function valid()
    {
        return isset($this->all()[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     */
    public function rewind()
    {
        $this->index = 0;
    }

    /**
     * Whether a offset exists
     * @param $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->results[$offset]);
    }

    /**
     * Offset to retrieve
     * @param $offset
     * @return
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @param $offset
     * @param $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        throw new \Exception('Cannot alter results of a query.');
    }

    /**
     * Offset to unset
     * @param $offset
     * @throws \Exception
     */
    public function offsetUnset($offset)
    {
        throw new \Exception('Cannot alter results of a query.');
    }
}
