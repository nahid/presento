<?php
declare(strict_types=1);

namespace Nahid\Presento\Tests;

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function camelCaseDataProvider(): array
    {
        return [
            // [ 'actual data', 'expected data' ]
            ["method", "Method"],
            ["double_word", "DoubleWord"],
            ["a_lot_of_words", "ALotOfWords"],
            ["FIX_CAPITALIZATION", "FixCapitalization"],
            ["this-should-work-too", "ThisShouldWorkToo", "-"]
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

    public function isCollectionDataProvider(): array
    {
        return [
            // [ 'actual data', 'expected data' ]
            ["scalar_data", false],
            [[], false],
            [[1, 2, 3], false],
            [[ 'numbers' => [ 1, 2, 3], 'names' => [ "john", "Doe"]], true]
        ];
    }

    /**
     * @dataProvider isCollectionDataProvider
     *
     * @param mixed $data
     * @param bool $expected
     */
    public function testIsCollectionMethod($data, bool $expected): void
    {
        $this->assertEquals($expected, is_collection($data));
    }
}