<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function camelCaseDataProvider()
    {
        return [
            ["method", "method"],
            ["double_word", "doubleWord"],
            ["a_lot_of_words", "aLotOfWords"],
            ["FIX_CAPITALIZATION", "fixCapitalization"],
            ["this-should-work-too", "thisShouldWorkToo", "-"]
        ];
    }

    /**
     * @dataProvider camelCaseDataProvider
     *
     * @param string $string
     * @param string $expected
     * @param string|null $delimiter
     */
    public function testToCamelCaseMethod(string $string, string $expected, string $delimiter=null): void
    {
        if ($delimiter) {
            $actual = to_camel_case($string, $delimiter);
        } else {
            $actual = to_camel_case($string);
        }

        $this->assertEquals($expected, $actual);
    }
}