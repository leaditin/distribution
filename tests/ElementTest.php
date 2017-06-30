<?php

declare(strict_types = 1);

namespace Leaditin\Distribution\Tests;

use Leaditin\Distribution\Element;
use PHPUnit\Framework\TestCase;

/**
 * Tests for {@see \Leaditin\Distribution\Element}
 *
 * @author Igor Vuckovic <igor@vuckovic.biz>
 */
class ElementTest extends TestCase
{
    /**
     * @param string $code
     * @dataProvider codeProvider
     */
    public function testElementCode(string $code)
    {
        $element = new Element($code, 25.32);

        self::assertSame($code, $element->getCode());
    }

    /**
     * @return array
     */
    public function codeProvider() : array
    {
        return [
            ['CODE'],
            ['A']
        ];
    }

    /**
     * @param float $value
     * @dataProvider valueProvider
     */
    public function testElementValue(float $value)
    {
        $element = new Element('CODE', $value);

        self::assertSame($value, $element->getValue());
    }

    /**
     * @return array
     */
    public function valueProvider() : array
    {
        return [
            [12.52],
            [50],
            [0.25]
        ];
    }
}
