<?php
namespace Cmis\Cmis\Types;

use IteratorAggregate;
use ArrayAccess;
use ArrayObject;

class Type implements IteratorAggregate, ArrayAccess
{
    /**
     * @var array */
    protected $attributes = null;

    protected $service = array('isPrimary', 'leftKey', 'rightKey', 'levelKey');
    /**
     * @var CommonProperties */
    protected $properties = null;

    public function __construct($options)
    {
        if (isset($options['propertyDefinitions'])) {
            $this->properties = $options['propertyDefinitions'];
            unset($options['propertyDefinitions']);
        }

        $this->attributes = $options;
        $this->service = array_flip($this->service);
        foreach($this->service as $key => &$value) {
            if (isset($this->attributes[$key])) {
                $value = $this->attributes[$key];
                unset($this->attributes[$key]);
            }
        }
    }

    public function isPrimary()
    {
        return $this->service['isPrimary'];
    }

    /**
     * @return CommonProperties */
    public function getProperties()
    {
        return $this->properties;
    }

    public function toArray()
    {
        $type = $this->attributes;
        $type['propertyDefinitions'] = $this->properties;
        return $type;
    }

    // <editor-fold defaultstate="collapsed" desc="Iterator & ArrayAccess">
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset)
    {
        if (isset($this->attributes[$offset])) {
            return $this->attributes[$offset];
        }
        if (isset($this->service[$offset])) {
            return $this->service[$offset];
        }
        return null;
    }

    public function offsetSet($offset, $value)
    {
        $this->attributes[$offset] = $value;
        return $this;
    }

    public function offsetUnset($offset)
    {
        if(isset($this->attributes[$offset])) {
            unset($this->attributes[$offset]);
        }
        return $this;
    }

    public function getIterator()
    {
        return $this->attributes->getIterator();
    }
    // </editor-fold>
}
