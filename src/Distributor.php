<?php

declare(strict_types = 1);

namespace Leaditin\Distribution;

use Leaditin\Distribution\Exception\DistributorException;

/**
 * Class Distribution
 *
 * @package Leaditin\Distribution
 * @author Igor Vuckovic <igor@vuckovic.biz>
 */
class Distributor
{
    /** @var array */
    private $probabilities;

    /** @var array */
    private $ranges;

    /**
     * @param Collection|Element[] $probabilities
     * @param int $numberOfElementsToDistribute
     */
    public function __construct(Collection $probabilities, int $numberOfElementsToDistribute)
    {
        $collection = $this->getNumerifiedCollection($probabilities, $numberOfElementsToDistribute);
        $this->probabilities = $collection->toArray();
        $this->setRanges($collection);
    }

    /**
     * @param array|null $excludeCodes
     * @return string
     * @throws DistributorException
     */
    public function useRandomCode(array $excludeCodes = null) : string
    {
        $possibleCodes = [];

        foreach ($this->probabilities as $code => $value) {
            if ($value <= 0 || ($excludeCodes !== null && in_array($code, $excludeCodes, true))) {
                continue;
            }

            $possibleCodes[$code] = $value;
        }

        if (empty($possibleCodes)) {
            throw DistributorException::exceededAllValues();
        }

        $code = $this->randomize($possibleCodes);
        $this->probabilities[$code]--;

        return (string)$code;
    }

    /**
     * @param string $code
     * @return string
     * @throws DistributorException
     */
    public function useCode(string $code) : string
    {
        if (!array_key_exists($code, $this->probabilities)) {
            throw DistributorException::undefinedCode($code);
        }

        if ($this->probabilities[$code] < 1) {
            throw DistributorException::exceededValuesForCode($code);
        }

        $this->probabilities[$code]--;

        return $code;
    }

    /**
     * @param Collection|Element[] $collection
     */
    private function setRanges(Collection $collection)
    {
        $min = 1;
        $collection = $this->getNumerifiedCollection($collection, 100)->getSortedCollection();

        foreach ($collection as $element) {
            if ($element->getValue() === 0.00) {
                continue;
            }

            $max = $min + $element->getValue() - 1;
            $this->ranges[$element->getCode()] = ['min' => $min, 'max' => $max];
            $min = $max + 1;
        }
    }

    /**
     * @param array $possibleCodes
     * @return string
     */
    private function randomize(array $possibleCodes) : string
    {
        $probabilityRanges = $this->ranges;
        foreach ($probabilityRanges as $code => $range) {
            if (!array_key_exists($code, $possibleCodes)) {
                unset($probabilityRanges[$code]);
            }
        }

        while (true) {
            $int = random_int(1, 100);
            foreach ($probabilityRanges as $code => $range) {
                if ($int >= $range['min'] && $int <= $range['max']) {
                    return (string)$code;
                }
            }
        }
    }

    /**
     * @param Collection|Element[] $collection
     * @param int $numberOfElementsToDistribute
     * @return Collection|Element[]
     */
    private function getNumerifiedCollection(Collection $collection, int $numberOfElementsToDistribute) : Collection
    {
        $elements = [];

        /** @var Collection $collection */
        $collection = $collection->getToppedUpCollection();
        if ($collection->count() > $numberOfElementsToDistribute) {
            $collection = $collection->getSortedCollection()->getReversedCollection();
        }

        foreach ($collection as $element) {
            if (count($elements) === $numberOfElementsToDistribute) {
                break;
            }

            if ($element->getValue() === 0.0) {
                $elements[] = new Element($element->getCode(), 0);
                continue;
            }

            $value = $numberOfElementsToDistribute * $element->getValue() / 100;
            $numeric = $value < 1 ? 1 : round($value);
            $elements[] = new Element($element->getCode(), $numeric);
        }

        return new Collection(...$elements);
    }
}
