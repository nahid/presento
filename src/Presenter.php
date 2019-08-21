<?php

namespace Nahid\Presento;


abstract class Presenter
{
    protected $aliases = [];
    protected $data = [];
    protected $generatedData = [];

    public function __construct($data)
    {
        $this->data = $data;
        $this->generatedData = $this->handle();
    }

    public function __invoke()
    {
        return $this->generatedData;
    }

    public function __toString()
    {
        return json_encode($this->generatedData);
    }

    abstract public function present();

    public function transformer()
    {
        return null;
    }

    public function handle()
    {
        if ($this->isCollection($this->data)) {
            $generatedData = [];
            foreach ($this->data  as $property => $data) {
                $generatedData[] = $this->transform($this->process($data));
            }

            return $generatedData;
        }

        return $this->transform($this->process($this->data));
    }

    public function process($data)
    {
        $present = $this->present();
        $record = [];

        foreach ($present as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            if (is_array($value) && count($value) == 2) {
                $presenter = new $value[1](get_from_array($data, $value[0]));
                $newVal = $value;
                if ($presenter instanceof Presenter) {
                    $newVal = $presenter->handle();
                }

                $record[$key] = $newVal;
            } else {
                $record[$key] = $value ? get_from_array($data, $value) : $value;
            }
        }

        return $record;
    }

    protected function transform($data)
    {
        $transformerClass = $this->transformer();

        if (!is_null($transformerClass)) {
            $transformer = new $transformerClass($data);
            return $transformer();
        }

        return $data;
    }

    public function toJson()
    {
        return json_encode($this->generatedData);
    }

    public function get()
    {
        return $this->generatedData;
    }

    /**
     * Check given value is multidimensional array
     *
     * @param array $arr
     * @return bool
     */
    protected function isCollection($arr)
    {
        if (!is_array($arr)) {
            return false;
        }

        return isset($arr[0]) && is_array($arr[0]);
    }


}