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
        $eligibleCodes = [];

        foreach ($this->probabilities as $code => $value) {
            if ($value <= 0 || in_array($code, (array)$excludeCodes, true)) {
                continue;
            }

            $eligibleCodes[$code] = $value;
        }

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
        $code = null;
        $probabilityRanges = $this->ranges;

        while (true) {
            $int = random_int(1, 100);
            foreach ($probabilityRanges as $code => $range) {
                if (!array_key_exists($code, $possibleCodes)) {
                    continue;
                }

                if ($int >= $range['min'] && $int <= $range['max']) {
                    break 2;
                }
            }
        }

        return (string)$code;
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
