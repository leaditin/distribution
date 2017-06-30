<?php

declare(strict_types = 1);

namespace Leaditin\Distribution\Tests;

use Leaditin\Distribution\Collection;
use Leaditin\Distribution\Distributor;
use Leaditin\Distribution\Element;
use Leaditin\Distribution\Exception\DistributorException;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \Leaditin\Distribution\Distributor}
 *
 * @author Igor Vuckovic <igor@vuckovic.biz>
 */
class DistributorTest extends TestCase
{
    const CODE_FEMALE = 'FEMALE';
    const CODE_MALE = 'MALE';

    public function testUseRandomCode()
    {
        $distributor = new Distributor(
            new Collection(
                new Element(self::CODE_FEMALE, 48),
                new Element(self::CODE_MALE, 52)
            ),
            2
        );

        $randomCode1 = $distributor->useRandomCode([self::CODE_FEMALE]);
        $randomCode2 = $distributor->useRandomCode();

        self::assertNotSame($randomCode1, $randomCode2);
        self::assertNotSame(self::CODE_FEMALE, $randomCode1);
        self::assertEquals([self::CODE_MALE, self::CODE_FEMALE], [$randomCode1, $randomCode2]);
    }

    public function testUseRandomCodeThrowsExceededAllValuesException()
    {
        $distributor = new Distributor(
            new Collection(
                new Element(self::CODE_FEMALE, 50),
                new Element(self::CODE_MALE, 50)
            ),
            0
        );

        $this->expectException(DistributorException::class);
        $this->expectExceptionMessage('Values for distribution are exceeded');

        $distributor->useRandomCode();
    }

    public function testUseCode()
    {
        $distributor = new Distributor(
            new Collection(
                new Element(self::CODE_FEMALE, 48),
                new Element(self::CODE_MALE, 52)
            ),
            2
        );

        $code = $distributor->useCode(self::CODE_MALE);

        self::assertSame(self::CODE_MALE, $code);
    }

    public function testUseCodeThrowsUndefinedCodeException()
    {
        $distributor = new Distributor(
            new Collection(
                new Element(self::CODE_FEMALE, 100),
                new Element(self::CODE_MALE, 0)
            ),
            2
        );

        $this->expectException(DistributorException::class);
        $this->expectExceptionMessage(sprintf('Trying to retrieve value for undefined code %s', 'UNKNOWN_CODE'));

        $distributor->useCode('UNKNOWN_CODE');
    }

    public function testUseCodeThrowsExceededValuesForCodeException()
    {
        $distributor = new Distributor(
            new Collection(
                new Element(self::CODE_MALE, 100)
            ),
            1
        );

        $this->expectException(DistributorException::class);
        $this->expectExceptionMessage(sprintf('Values for code %s are exceeded', self::CODE_MALE));

        $distributor->useCode(self::CODE_MALE);
        $distributor->useCode(self::CODE_MALE);
    }
}
