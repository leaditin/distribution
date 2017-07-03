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
     * @param Collection $probabilities
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
        $eligibleCodes = $this->getEligibleCodes($excludeCodes);

        if (empty($eligibleCodes)) {
            throw DistributorException::exceededAllValues();
        }

        $code = $this->randomize($eligibleCodes);
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
     * @param Collection $collection
     */
    private function setRanges(Collection $collection)
    {
        $min = 1;
        $sortedCollection = $this
            ->getNumerifiedCollection($collection, 100)
            ->getSortedCollection();

        foreach ($sortedCollection as $element) {
            if ($element->getValue() === 0.00) {
                continue;
            }

            $max = $min + $element->getValue() - 1;
            $this->ranges[$element->getCode()] = ['min' => $min, 'max' => $max];
            $min = $max + 1;
        }
    }


    /**
     * @param array|null $excludeCodes
     * @return array
     */
    private function getEligibleCodes(array $excludeCodes = null) : array
    {
        $eligibleCodes = [];

        foreach ($this->probabilities as $code => $value) {
            if ($value <= 0 || in_array($code, (array)$excludeCodes, true)) {
                continue;
            }

            $eligibleCodes[$code] = $value;
        }

        return $eligibleCodes;
    }

    /**
     * @param array $possibleCodes
     * @return string
     */
    private function randomize(array $possibleCodes) : string
    {
        $probabilityRanges = array_intersect_key($this->ranges, $possibleCodes);

        do {
            $code = $this->getRandomCodeFromRanges($probabilityRanges);
        } while ($code === false);

        return (string)$code;
    }

    /**
     * @param array $ranges
     * @return bool|string
     */
    private function getRandomCodeFromRanges(array $ranges)
    {
        $int = random_int(1, 100);

        foreach ($ranges as $code => $range) {
            if ($int >= $range['min'] && $int <= $range['max']) {
                return $code;
            }
        }

        return false;
    }

    /**
     * @param Collection $probabilities
     * @param int $numberOfElementsToDistribute
     * @return Collection
     */
    private function getNumerifiedCollection(Collection $probabilities, int $numberOfElementsToDistribute) : Collection
    {
        $elements = [];
        $collection = $probabilities
            ->getToppedUpCollection()
            ->getSortedCollection()
            ->getReversedCollection();

        foreach ($collection as $element) {
            $elements[] = new Element(
                $element->getCode(),
                $this->deriveNumericValue($element, $numberOfElementsToDistribute)
            );
        }

        $elements = array_slice($elements, 0, $numberOfElementsToDistribute);

        return new Collection(...$elements);
    }

    /**
     * @param Element $element
     * @param int $numberOfElementsToDistribute
     * @return float
     */
    private function deriveNumericValue(Element $element, int $numberOfElementsToDistribute) : float
    {
        if ($element->getValue() === 0.00) {
            return 0;
        }

        $value = $numberOfElementsToDistribute * $element->getValue() / 100;

        return $value < 1 ? 1 : round($value);
    }
}
