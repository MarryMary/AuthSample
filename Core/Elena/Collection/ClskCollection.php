<?php

namespace Clsk\Elena\Collection;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;

class ClskCollection implements ArrayAccess, IteratorAggregate
{

    /**
     * @var array
     */
    protected $items = [];

    public function __construct($items = [])
    {

        if (is_array($items)) {
            $this->items = $items;
        }

        if ($items instanceof self) {
            $this->items = $items->all();
        }

        $this->items = (array)$items;
    }

    public function all()
    {
        return $this->items;
    }

    public function count()
    {
        return count($this->items);
    }

    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->items);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

}