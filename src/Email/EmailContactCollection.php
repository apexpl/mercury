<?php
declare(strict_types = 1);

namespace Apex\Mercury\Email;

use Apex\App\Base\DataTypes\BaseIterator;
use Apex\Mercury\Email\EmailContact;
use Apex\Db\Mapper\FromInstance;

/**
 * E-mail contact iterator
 */
class EmailContactCollection           implements \ArrayAccess, \Iterator, \Countable
{

    /**
     * Constructor
     */
    public function __construct(
        private array $items = []
    ) { 

        // Ensure any items passed match item class
        foreach ($this->items as $value) { 
            if (!$value instanceof EmailContact) {
                $class = is_object($value) ? $value::class : GetType($value);
                throw new \InvalidArgumentException("The class " . __CLASS__ . " only allows items of " . EmailContact::class . " but received item of $class");
            }
        }

    }


    /**
     * Set offset
     */
    public function offsetSet(mixed $offset, mixed $value):void
    {

        // Enforce item_class
        if (!$value instanceof EmailContact) { 
            $class = is_object($value) ? $value::class : GetType($value);
            throw new \InvalidArgumentException("The class " . __CLASS__ . " only allows items of " . EmailContact::$item_class . " but received item of $class");
        }

        // Add to items
        if (is_null($offset)) { 
            $this->items[] = $value;
        } else { 
            $this->items[$offset] = $value;
        }

    }

    /**
     * Offset exists
     */
    public function offsetExists(mixed $offset):bool
    {
        return isset($this->items[$offset]);
    }

    /**
     * Offset unset
     */
    public function offsetUnset(mixed $offset):void
    {
        unset($this->items[$offset]);
    }

    /**
     * Offset get
     */
    public function offsetGet(mixed $offset):mixed
    {
        return isset($this->items[$offset]) ? $this->items[$offset] : null;
    }

    /**
     * Rewind
     */
    public function rewind():void
    {
        $this->position = 0;
    }

    /**
     * Current
     */
    public function current():mixed
    {
        return isset($this->items[$this->position]) ? $this->items[$this->position] : null;
    }

    /**
     * Key
     */
    public function key():mixed
    {
        return $this->position;
    }

    /**
     * Next
     */
    public function next():void
    {
        ++$this->position;
    }

    /**
     * Valid
     */
    public function valid():bool
    {
        return $this->position >= count($this->items) ? false : true;
    }

    /**
     * Count
     */
    public function count():int
    {
        return count($this->items);
    }

}


