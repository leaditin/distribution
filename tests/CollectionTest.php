<?php

declare(strict_types = 1);

namespace Leaditin\Distribution\Tests;

use Leaditin\Distribution\Collection;
use Leaditin\Distribution\Element;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \Leaditin\Distribution\Collection}
 *
 * @author Igor Vuckovic <igor@vuckovic.biz>
 */
class CollectionTest extends TestCase
{
    public function testFirst()
    {
        $europe = new Element('EUROPE', 7.25);
        $asia = new Element('ASIA', 36.25);
        $america = new Element('AMERICA', 28.36);
        $oceania = new Element('OCEANIA', 28.14);

        $collection = new Collection($europe, $asia, $america, $oceania);

        self::assertSame($europe, $collection->first());
    }

    public function testLast()
    {
        $europe = new Element('EUROPE', 7.25);
        $asia = new Element('ASIA', 36.25);
        $america = new Element('AMERICA', 28.36);
        $oceania = new Element('OCEANIA', 28.14);

        $collection = new Collection($europe, $asia, $america, $oceania);

        self::assertSame($oceania, $collection->last());
    }

    public function testMax()
    {
        $europe = new Element('EUROPE', 7.25);
        $asia = new Element('ASIA', 36.25);
        $america = new Element('AMERICA', 28.36);
        $oceania = new Element('OCEANIA', 28.14);

        $collection = new Collection($europe, $asia, $america, $oceania);
        $max = $collection->max();

        self::assertEquals($asia, $max);
        self::assertNotSame($asia, $max);
    }

    public function testMin()
    {
        $europe = new Element('EUROPE', 7.25);
        $asia = new Element('ASIA', 36.25);
        $america = new Element('AMERICA', 28.36);
        $oceania = new Element('OCEANIA', 28.14);

        $collection = new Collection($europe, $asia, $america, $oceania);
        $min = $collection->min();

        self::assertEquals($europe, $min);
        self::assertNotSame($europe, $min);
    }

    public function testRandom()
    {
        $europe = new Element('EUROPE', 7.25);
        $asia = new Element('ASIA', 36.25);
        $america = new Element('AMERICA', 28.36);
        $oceania = new Element('OCEANIA', 28.14);

        $collection = new Collection($europe, $asia, $america, $oceania);

        self::assertInstanceOf(Element::class, $collection->random());
    }

    public function testRandomIsNull()
    {
        $collection = new Collection();

        self::assertNull($collection->random());
    }

    /**
     * @param Collection $collection
     * @dataProvider getToppedCollectionProvider
     */
    public function testGetToppedUpCollection(Collection $collection)
    {
        $toppedUpCollection = $collection->getToppedUpCollection();

        self::assertInstanceOf(Collection::class, $toppedUpCollection);
        self::assertSame(100.00, $toppedUpCollection->getSum());
    }

    /**
     * @return array
     */
    public function getToppedCollectionProvider() : array
    {
        return [
            [
                new Collection(
                    new Element('EUROPE', 7.25),
                    new Element('ASIA', 36.25)
                )
            ],
            [
                new Collection(
                    new Element('OCEANIA', 24.25),
                    new Element('ASIA', 75.75)
                )
            ],
            [
                new Collection(
                    new Element('EUROPE', 0),
                    new Element('AMERICA', 0)
                )
            ],
            [
                new Collection(
                    new Element('AMERICA', 55.17),
                    new Element('OCEANIA', 23.17),
                    new Element('OCEANIA', 23.17)
                )
            ],
        ];
    }

    public function testGetSortedCollection()
    {
        $europe = new Element('EUROPE', 7.25);
        $asia = new Element('ASIA', 36.25);
        $america = new Element('AMERICA', 28.36);
        $oceania = new Element('OCEANIA', 28.14);

        $collection = new Collection($europe, $asia, $america, $oceania);
        $sortedCollection = $collection->getSortedCollection();

        self::assertInstanceOf(Collection::class, $sortedCollection);
        self::assertEquals($europe, $sortedCollection->first());
        self::assertEquals($asia, $sortedCollection->last());
    }

    public function testGetRevertedCollection()
    {
        $europe = new Element('EUROPE', 7.25);
        $asia = new Element('ASIA', 36.25);
        $america = new Element('AMERICA', 28.36);
        $oceania = new Element('OCEANIA', 28.14);

        $collection = new Collection($europe, $asia, $america, $oceania);
        $sortedCollection = $collection->getReversedCollection();

        self::assertInstanceOf(Collection::class, $sortedCollection);
        self::assertEquals($oceania, $sortedCollection->first());
        self::assertEquals($europe, $sortedCollection->last());
    }

    public function testCount()
    {
        $collection = $this->getSampleCollection();

        self::assertSame(4, $collection->count());
    }

    public function testGetIterator()
    {
        $collection = new Collection();

        self::assertInstanceOf(\ArrayIterator::class, $collection->getIterator());
    }

    public function testToArray()
    {
        $collection = $this->getSampleCollection();

        self::assertCount(4, $collection->toArray());
    }

    /**
     * @return Collection|Element[]
     */
    private function getSampleCollection() : Collection
    {
        return new Collection(
            new Element('EUROPE', 7.25),
            new Element('ASIA', 36.25),
            new Element('AMERICA', 28.36),
            new Element('OCEANIA', 28.14)
        );
    }
}
