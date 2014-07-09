<?php

namespace Application\Common\Collections;


use Doctrine\Common\Collections\ArrayCollection;
use Closure;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\ClosureExpressionVisitor;

abstract class DirectoryArrayCollection extends ArrayCollection
{
    protected $_basedir;
    protected $_files = array();

    /**
     * @param string $basedir
     * @param array $elements
     * @param boolean $internal Flag specifying that the constructor got invoked from DirectoryArrayCollection, this is required because methods invoking the constructor could pass an empty elements array which would lead to the invocation of reload().
     */
    function __construct($basedir, array $elements = array(), $internal = false) {
        if (! empty($elements) || $internal) {
            parent::__construct($elements);
        } else {
            $this->_basedir = $basedir;
            $this->reload();
        }
    }

    /**
     * Removes previously added elements and updates the files array.
     */
    protected function reset() {

        // reset the elements and files array
        $this->clear();
        $this->_files = array();

        $non_hidden = glob($this->_basedir . '/*');

        // if there are no non-hidden token files $nonhidden is not an array
        if (is_array($non_hidden)) {
            $this->_files = $non_hidden;
        }

        $hidden = glob($this->_basedir . '/.*');

        if (is_array($hidden)) {
            foreach ($hidden as $h) {
                if (basename($h) !== '.' && basename($h) !== '..') {
                    $this->_files[] = $h;
                }
            }
        }
    }

    /**
     * Sub-classes should call the reset method before adding new elements.
     */
    abstract protected function reload();

    /**
     * @inheritdoc
     */
    public function map(Closure $func)
    {
        return new static(array_map($func, parent::toArray()), $this->_basedir, true);
    }

    /**
     * @inheritdoc
     */
    public function filter(Closure $p)
    {
        return new static($this->_basedir, array_filter(parent::toArray(), $p), true);
    }

    /**
     * @inheritdoc
     */
    public function partition(Closure $p)
    {
        $coll1 = $coll2 = array();
        foreach (parent::toArray() as $key => $element) {
            if ($p($key, $element)) {
                $coll1[$key] = $element;
            } else {
                $coll2[$key] = $element;
            }
        }
        return array(new static($this->_basedir, $coll1, true), new static($this->_basedir, $coll2, true));
    }

    /**
     * @inheritdoc
     */
    public function matching(Criteria $criteria)
    {
        $expr     = $criteria->getWhereExpression();
        $filtered = parent::toArray();

        if ($expr) {
            $visitor  = new ClosureExpressionVisitor();
            $filter   = $visitor->dispatch($expr);

            $filtered = array_filter($filtered, $filter);
        }

        if ($orderings = $criteria->getOrderings()) {
            $next = null;
            foreach (array_reverse($orderings) as $field => $ordering) {
                $next = ClosureExpressionVisitor::sortByField($field, $ordering == 'DESC' ? -1 : 1, $next);
            }

            usort($filtered, $next);
        }

        $offset = $criteria->getFirstResult();
        $length = $criteria->getMaxResults();

        if ($offset || $length) {
            $filtered = array_slice($filtered, (int)$offset, $length);
        }

        return new static($this->_basedir, $filtered, true);
    }
}
