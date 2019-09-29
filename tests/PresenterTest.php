<?php declare(strict_types=1);

namespace Nahid\Presento\Tests;

use Nahid\Presento\Presenter;
use PHPUnit\Framework\TestCase;

final class PresenterTest extends TestCase
{
    public static $sampleData = [
        "id" => 123456,
        "name" => "Nahid Bin Azhar",
        "email" => "talk@nahid.im",
        "type" => 1,
        "is_active" => 1,
        "created_at" => "2018-01-02 02:03:04",
        "updated_at" => "2018-01-02 02:03:04",
        "deleted_at" => "2018-01-02 02:03:04",
        "projects" => [
            [
                "id" => 1,
                "name" => "Laravel Talk",
                "url"   => "https://github.com/nahid/talk",
                "license" => "CC0",
                "created_at" => "2016-02-02 02:03:04"
            ],
            [
                "id" => 2,
                "name" => "JsonQ",
                "url"   => "https://github.com/nahid/jsonq",
                "license" => "MIT",
                "created_at" => "2018-01-02 02:03:04"
            ]
        ]
    ];

    function arrayEqual(array $arr1, array $arr2): bool
    {
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

    public function test_presenter_returns_only_selected_fields(): void
    {
        $presenter = new TestSimplePresenterObject(static::$sampleData);
        $expected = [
            "id" => 123456,
            "name" => "Nahid Bin Azhar",
            "email" => "talk@nahid.im",
            "type" => 1,
            "is_active" => 1,
        ];

        $this->assertTrue($this->arrayEqual($presenter->get(), $expected));
    }

    public function test_presenter_returns_non_exists_fields_value_null(): void
    {
        $data = [
          'id' => 1
        ];

        $presenter = new TestPresenterWithNonExistsFieldsObject($data);
        $expected = [
            "name" => null,
            "email" => null
        ];

        $this->assertTrue($this->arrayEqual($presenter->get(), $expected));
    }
}

class TestSimplePresenterObject extends Presenter
{
    public function present() : array
    {
        return [
            'id',
            'name',
            'email',
            'type',
            'is_active'
        ];
    }
}

class TestPresenterWithNonExistsFieldsObject extends Presenter
{
    public function present() : array
    {
        return [
            'name',
            'email'
        ];
    }
}