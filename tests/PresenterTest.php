<?php declare(strict_types=1);

namespace Nahid\Presento\Tests;

use Nahid\Presento\Presenter;
use PHPUnit\Framework\TestCase;

final class PresenterTest extends TestCase
{

    /**
     * @var Presenter
     */

    protected function classInstance($data = null, $transformer = null, $present = [])
    {
        // Create a new instance from the Abstract Class
        $presenter = new class($data, $transformer, $present) extends Presenter{
            protected $_present;
            // Just a sample public function that returns this anonymous instance
            public function __construct($data, $transformer, $present)
            {
                $this->_present = $present;
                parent::__construct($data, $transformer);
            }
            public function present() : array
            {
                return $this->_present;
            }
        };

        $presenter->setPresent($data);

        return $presenter;
    }

    function arrayEqual($arr1, $arr2) {
        foreach($arr1 as $k => $v) {
            if (!array_key_exists($k, $arr2)) {
                return false;
            }

            if (!is_array($v)) {
                if ($v !== $arr2[$k]) {
                    return false;
                }
            }

            if (is_array($v)) {
                 $resp = $this->arrayEqual($v, $arr2[$k]);

                 if (!$resp) {
                     return false;
                 }
            }

        }

        return true;
    }


    public function getWithoutTransformerDataProvider(): array
    {
        return [
            // ['data' 'presenter data, 'expected data']
            [[],['name', 'email'], null],
            [['id'=>1],['name', 'email'], ['name' => null, 'email' => null]],
            [['id'=>1, 'name' => 'Alien'],['name', 'email'], ['name' => 'Alien', 'email' => null]],
            [['id'=>1, 'name' => 'Alien', 'email' => 'alien@example.com'],['name', 'email'], ['name' => 'Alien', 'email' => 'alien@example.com']],
            [['id'=>1, 'name' => 'Alien', 'emails' => ['alien@example.com']],['name', 'emails'], ['name' => 'Alien', 'emails' => ['alien@example.com']]],
            [['id'=>1, 'name' => 'Alien', 'email' => 'alien@example.com'],['name', 'emails'], ['name' => 'Alien', 'emails' => null]],
        ];
    }

    /**
     * @dataProvider getWithoutTransformerDataProvider
     *
     * @param mixed $data
     * @param array $present
     * @param mixed $expected
     * @return bool
     */
    public function testGetWithoutTransformerMethod($data, array $present, $expected): bool
    {
        $presenter = $this->classInstance($data, null, $present);
        $actual = $presenter->get();
        if (is_null($expected)) {
            $this->assertEquals($actual, $expected);
            return true;
        }
        $this->assertTrue($this->arrayEqual($presenter->get(), $expected));
        return true;
    }
}