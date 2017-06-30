<?php

declare(strict_types = 1);

namespace Leaditin\Distribution;

/**
 * Class Element
 *
 * @package Leaditin\Distribution
 * @author Igor Vuckovic <igor@vuckovic.biz>
 */
class Element
{
    /** @var string */
    private $code;

    /** @var float */
    private $value;

    /**
     * @param string $code
     * @param float $value
     */
    public function __construct(string $code, float $value)
    {
        $this->code = $code;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getCode() : string
    {
        return $this->code;
    }

    /**
     * @return float
     */
    public function getValue() : float
    {
        return $this->value;
    }
}
