<?php
namespace Cmis\Cmis\Types;

use IteratorAggregate;
use ArrayAccess;

class Properties implements IteratorAggregate, ArrayAccess
{
    const OWN_PROPERTIES = 'OWN_PROPERTIES';

    /**
     * @var \ArrayObject */
    protected $properties = array();
    protected $iterator   = null;

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function toArray($flag = null)
    {
        if ($flag == self::OWN_PROPERTIES) {
            $res = array();
            foreach($this->properties as $k=>$v) {
                if ($v['typeId'] == $v['typeOwnerId']) {
                    $res[$k] = $v;
                }
            }
            return $res;
        }
        return $this->properties->getArrayCopy();
    }

    /**
     * @return ArrayIterator */
    public function getIterator()
    {
        if (!$this->iterator) {
            $this->iterator = new \ArrayIterator($this->properties);
        }
        return $this->iterator;
    }

    public function offsetExists($offset)
    {
        return isset($this->properties[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->properties[$offset])) {
            return isset($this->properties[$offset]);
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->properties[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->properties[$offset]);
    }
}
