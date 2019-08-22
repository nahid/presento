<?php declare(strict_types = 1);

namespace Nahid\Presento;


abstract class Presenter
{
    protected $aliases = [];
    protected $data = [];
    protected $generatedData = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->generatedData = $this->handle();
    }

    public function __invoke() : array
    {
        return $this->generatedData;
    }

    public function __toString() : string
    {
        return json_encode($this->generatedData);
    }

    abstract public function present() : array;

    /**
     * get transformer name, this method can be override
     *
     * @return null|string
     */
    public function transformer()
    {
        return null;
    }

    /**
     * handle data based on presented data
     *
     * @return array
     */
    public function handle() : array
    {
        if ($this->isCollection($this->data)) {
            $generatedData = [];
            foreach ($this->data  as $property => $data) {
                $generatedData[$property] = $this->transform($this->process($data));
            }

            return $generatedData;
        }

        return $this->transform($this->process($this->data));
    }

    /**
     * process data based presented data model
     *
     * @param array $data
     * @return array
     */
    public function process(array $data) : array
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

    /**
     * transform given data based on transformer.
     *
     * @param array $data
     * @return array
     */
    protected function transform(array $data) : array
    {
        $transformerClass = $this->transformer();

        if (!is_null($transformerClass)) {
            $transformer = new $transformerClass($data);
            return $transformer();
        }

        return $data;
    }

    /**
     * get generated data as json string
     *
     * @return string
     */
    public function toJson() : string
    {
        return json_encode($this->generatedData);
    }

    /**
     * get full set of data as array
     *
     * @return array
     */
    public function get() : array
    {
        return $this->generatedData;
    }

    /**
     * Check given value is multidimensional array
     *
     * @param array $arr
     * @return bool
     */
    protected function isCollection(array $arr) : bool
    {
        if (!is_array($arr)) {
            return false;
        }

        return isset($arr[0]) && is_array($arr[0]);
    }
}