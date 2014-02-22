<?php

namespace Application\Map;

use \Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use JMS\Serializer\Annotation as JMS;
use Closure;

/**
 * An abstract list to be subclassed in serializable models.
 *
 * @see documentation of the experiment 'jms_serializer'.
 * @JMS\ExclusionPolicy("none")
 */
abstract class AbstractList extends ArrayCollection
{
    /**
     * @JMS\Type("array")
     */
    private $_elements;

    function __construct()
    {
        $this->spacemap = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->spacemap->__toString();
    }

    public function toArray()
    {
        return $this->spacemap->toArray();
    }

    public function first()
    {
        return $this->spacemap->first();
    }

    public function last()
    {
        return $this->spacemap->last();
    }

    public function key()
    {
        return $this->spacemap->key();
    }

    public function next()
    {
        return $this->spacemap->next();
    }

    public function current()
    {
        return $this->spacemap->current();
    }

    public function remove($key)
    {
        return $this->spacemap->remove($key);
    }

    public function removeElement($element)
    {
        return $this->spacemap->removeElement($element);
    }

    public function offsetExists($offset)
    {
        return $this->spacemap->offsetExists($offset);
    }

    public function offsetGet($offset)
    {
        return $this->spacemap->offsetGet($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->spacemap->offsetSet($offset, $value);
    }

    public function offsetUnset($offset)
    {
        return $this->spacemap->offsetUnset($offset);
    }

    public function containsKey($key)
    {
        return $this->spacemap->containsKey($key);
    }

    public function contains($element)
    {
        return $this->spacemap->contains($element);
    }

    public function exists(Closure $p)
    {
        return $this->spacemap->exists($p);
    }

    public function indexOf($element)
    {
        return $this->spacemap->indexOf($element);
    }

    public function get($key)
    {
        return $this->spacemap->get($key);
    }

    public function getKeys()
    {
        return $this->spacemap->getKeys();
    }

    public function getValues()
    {
        return $this->spacemap->getValues();
    }

    public function count()
    {
        return $this->spacemap->count();
    }

    public function set($key, $value)
    {
        $this->spacemap->set($key, $value);
    }

    public function add($value)
    {
        return $this->spacemap->add($value);
    }

    public function isEmpty()
    {
        return $this->spacemap->isEmpty();
    }

    public function getIterator()
    {
        return $this->spacemap->getIterator();
    }

    public function map(Closure $func)
    {
        return $this->spacemap->map($func);
    }

    public function filter(Closure $p)
    {
        return $this->spacemap->filter($p);
    }

    public function forAll(Closure $p)
    {
        return $this->spacemap->forAll($p);
    }

    public function partition(Closure $p)
    {
        return $this->spacemap->partition($p);
    }

    public function clear()
    {
        $this->spacemap->clear();
    }

    /**
     * @param int $offset
     * @param null $length
     * @return array
     */
    public function slice($offset, $length = null)
    {
        return $this->spacemap->slice($offset, $length);
    }

    /**
     * @param Criteria $criteria
     * @return Collection
     */
    public function matching(Criteria $criteria)
    {
        $collection = $this->spacemap->matching($criteria);
        return new static($collection);
    }

}