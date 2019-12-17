<?php

namespace App\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ExtendedArrayCollection extends ArrayCollection
{
    /**
     * Reduce the collection into a single value.
     *
     * @param \Closure $func
     * @param null $initialValue
     * @return mixed
     */
    public function reduce(\Closure $func, $initialValue = null)
    {
        return array_reduce($this->toArray(), $func, $initialValue);
    }

    /**
     * Apply filter chain
     *
     * @return ExtendedArrayCollection
     * @var \Closure[] $chain
     */
    public function applyFilterChain($chain)
    {
        $collection = $this;
        foreach ($chain as $filter) {
            $collection = $collection->filter($filter);
        }
        return $collection;
    }

    /**
     * Get average
     * @param $field
     * @return float
     */
    public function getAverage($field)
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor();
        $avg = 0;
        if ($this->count() === 0) {
            return 0;
        }
        $this->forAll(function ($index, $element) use (&$avg, $accessor, $field) {
            $avg += $accessor->getValue($element, $field);
            return true;
        });
        return $avg / $this->count();
    }

    /**
     * Get median.
     *
     * @param $field
     * @return float
     */
    public function getMedian($field)
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor();
        $iterator = $this->getSortedIteratorOn($field);
        if ($iterator->count() === 0) {
            return 0;
        }
        if ($iterator->count() % 2 === 0) {
            return (
                    $accessor->getValue($iterator[ceil($iterator->count() / 2) - 1], $field) +
                    $accessor->getValue($iterator[ceil($iterator->count() / 2)], $field)
                ) / 2;
        } else {
            return $accessor->getValue($iterator[floor($iterator->count() / 2)], $field);
        }
    }

    /**
     * Get absolute median. Make negative into position
     *
     * @param $field
     * @return float
     */
    public function getAbsoluteMean($field)
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor();
        $iterator = $this->getSortedAbsoluteIteratorOn($field);
        if ($iterator->count() === 0) {
            return 0;
        }
        if ($iterator->count() % 2 === 0) {
            return (
                    abs($accessor->getValue($iterator[ceil($iterator->count() / 2) - 1], $field)) +
                    abs($accessor->getValue($iterator[ceil($iterator->count() / 2)], $field))
                ) / 2;
        } else {
            return abs($accessor->getValue($iterator[floor($iterator->count() / 2)], $field));
        }
    }

    public function getSortedAbsoluteIteratorOn($field)
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor();
        $iterator = $this->getIterator();
        $iterator->uasort(function ($first, $second) use ($accessor, $field) {
            $firstPrice = abs($accessor->getValue($first, $field));
            $secondPrice = abs($accessor->getValue($second, $field));
            if ($firstPrice == $secondPrice) {
                return 0;
            } else {
                return ($firstPrice < $secondPrice ? -1 : 1);
            }
        });
        $list = iterator_to_array($iterator, false);
        return new \ArrayIterator($list);
    }

    /**
     * Get a sorted iterator on a field.
     *
     * @param string $field
     * @return \ArrayIterator
     */
    public function getSortedIteratorOn($field)
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor();
        $iterator = $this->getIterator();
        $iterator->uasort(function ($first, $second) use ($accessor, $field) {
            $firstPrice = $accessor->getValue($first, $field);
            $secondPrice = $accessor->getValue($second, $field);
            if ($firstPrice == $secondPrice) {
                return 0;
            } else {
                return ($firstPrice < $secondPrice ? -1 : 1);
            }
        });
        $list = iterator_to_array($iterator, false);
        return new \ArrayIterator($list);
    }

    /**
     * Return a new collection with sorted result.
     *
     * @param string $field
     * @return PropertyCollection
     */
    public function getSortedCollection($field)
    {
        return new self($this->getSortedIteratorOn($field)->getArrayCopy());
    }

    /**
     * Get standard deviation.
     *
     * @param $field
     * @return float
     */
    public function getStandardDeviation($field)
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor();
        $average = $this->getAverage($field);
        $variance = 0;
        if ($this->count() === 0) {
            return 0;
        }
        $this->forAll(function ($index, $element) use (&$variance, $average, $accessor, $field) {
            $variance += pow($accessor->getValue($element, $field) - $average, 2);
            return true;
        });
        $variance /= $this->count();
        return sqrt($variance);
    }

    /**
     * Return a new collection excluding the outlier.
     *
     * @param $median
     * @param float $sdRange The Standard Deviation range.
     * @param string $field
     * @return self
     */
    public function excludeOutlier($median, $sdRange, $field)
    {
        $accessor = PropertyAccess::createPropertyAccessorBuilder()->enableMagicCall()->getPropertyAccessor();
        $from = $median / 2;
        $to = $median + $sdRange;
        return $this->filter(function ($e) use ($from, $to, $accessor, $field) {
            $price = 0;
            if ($accessor->getValue($e, $field) !== null) {
                $price = $accessor->getValue($e, $field);
            }
            if ($price >= $from && $price <= $to) {
                return true;
            } else {
                return false;
            }
        });
    }
}
