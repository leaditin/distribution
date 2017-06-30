<?php

declare(strict_types = 1);

namespace Leaditin\Distribution;

/**
 * Class Collection
 *
 * @package Leaditin\Distribution
 * @author Igor Vuckovic <igor@vuckovic.biz>
 */
class Collection implements \Countable, \IteratorAggregate
{
    /** @var Element[] */
    private $elements;

    /**
     * @param Element[] ...$elements
     */
    public function __construct(Element ...$elements)
    {
        $this->elements = $elements;
    }

    /**
     * Returns first element from collection
     *
     * @return Element|null
     */
    public function first()
    {
        $elements = $this->elements;

        return !empty($elements) ? reset($elements) : null;
    }

    /**
     * Returns last element from collection
     *
     * @return Element|null
     */
    public function last()
    {
        $elements = $this->elements;

        return !empty($elements) ? end($elements) : null;
    }

    /**
     * Returns element with the highest value
     *
     * @return Element|null
     */
    public function max()
    {
        $collection = $this->getSortedCollection();

        return $collection->last();
    }

    /**
     * Returns element with the lowest value
     *
     * @return Element|null
     */
    public function min()
    {
        $collection = $this->getSortedCollection();

        return $collection->first();
    }

    /**
     * Returns random element from collection
     *
     * @return Element|null
     */
    public function random()
    {
        if ($this->count() === 0) {
            return null;
        }

        $elements = $this->elements;

        return $elements[array_rand($elements)];
    }

    /**
     * Returns new collection with elements where sum of all is equals to 100
     * Each element is modified by proportional modifier based on it's current value
     *
     * @return Collection|Element[]
     */
    public function getToppedUpCollection() : Collection
    {
        $sum = round($this->getSum(), 2);
        $toppedUpCollection = new self();

        foreach ($this->elements as $element) {
            $modifier = ($sum === 0.0) ? 100 / count($this->elements) : $element->getValue() / $sum * (100 - $sum);
            $percentage = $element->getValue() + $modifier;
            $toppedUpCollection->elements[] = new Element($element->getCode(), $percentage);
        }

        return $toppedUpCollection;
    }

    /**
     * Returns new collection with elements sorted ascending based on theirs percentage
     *
     * @return Collection|Element[]
     */
    public function getSortedCollection() : Collection
    {
        $tmp = [];

        foreach ($this->elements as $element) {
            $tmp[$element->getCode()] = $element->getValue();
        }

        asort($tmp, SORT_NUMERIC);
        $sorted = new self();

        foreach ($tmp as $code => $percentage) {
            $sorted->elements[] = new Element((string)$code, $percentage);
        }

        return $sorted;
    }

    /**
     * Returns new collection with elements in reverse order
     *
     * @return Collection|Element[]
     */
    public function getReversedCollection() : Collection
    {
        $reverse = new self();
        $reverse->elements = array_reverse($this->elements);

        return $reverse;
    }

    /**
     * Returns sum of all elements
     *
     * @return float
     */
    public function getSum() : float
    {
        $sum = 0;

        foreach ($this->elements as $element) {
            $sum += $element->getValue();
        }

        return $sum;
    }

    /**
     * Returns multidimensional array of all elements where each is associative array of element's code and value
     *
     * @return array
     */
    public function toArray() : array
    {
        $array = [];

        foreach ($this->elements as $element) {
            $array[$element->getCode()] = $element->getValue();
        }

        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }
}
