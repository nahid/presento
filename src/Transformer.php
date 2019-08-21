<?php

namespace Nahid\Presento;


abstract class Transformer
{
    protected $generatedData = [];
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
        $this->transform();
    }

    public function __invoke()
    {
        return $this->getData();
    }

    public function transform()
    {
        foreach ($this->data as $key => $value) {
            $this->generatedData[$key] = $this->callPropertyFunction($key, $value);
        }
    }

    protected function isPropertyNeedProcess($property)
    {
        $method = $this->getPropertyFunction($property);

        return method_exists($this, $method);
    }

    protected function getPropertyFunction($property)
    {
        return 'get'. to_camel_case($property) . 'Property';
    }

    protected function callPropertyFunction($property, $value)
    {
        if ($this->isPropertyNeedProcess($property)) {
            return call_user_func_array([$this, $this->getPropertyFunction($property)], [$value]);
        }

        return $value;
    }

    public function getProperty($property)
    {
        return get_from_array($this->data, $property);
    }

    public function getData()
    {
        return $this->generatedData;
    }

}