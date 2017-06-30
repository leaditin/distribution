<?php

declare(strict_types = 1);

namespace Leaditin\Distribution\Exception;

/**
 * Class DistributorException
 *
 * @package Leaditin\Distribution\Exception
 * @author Igor Vuckovic <igor@vuckovic.biz>
 */
class DistributorException extends \LogicException
{
    /**
     * @param string $code
     * @return DistributorException
     */
    public static function undefinedCode(string $code) : DistributorException
    {
        return new self(sprintf('Trying to retrieve value for undefined code %s', $code));
    }

    /**
     * @param string $code
     * @return DistributorException
     */
    public static function exceededValuesForCode(string $code) : DistributorException
    {
        return new self(sprintf('Values for code %s are exceeded', $code));
    }

    /**
     * @return DistributorException
     */
    public static function exceededAllValues() : DistributorException
    {
        return new self('Values for distribution are exceeded');
    }
}
