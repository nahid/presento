<?php declare(strict_types = 1);

namespace Nahid\Presento;


abstract class Presenter
{
    protected $transformer = null;
    protected $data = [];
    protected $generatedData = [];

    public function __construct(array $data, string $transformer = null)
    {
        $this->data = $data;
        $this->transformer = $this->transformer();
        if (!is_null($transformer)) {
            $this->transformer = $transformer;
        }

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
        if (is_collection($this->data)) {
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

        if (count($present) == 0) {
            $record = $data;
        }

        foreach ($present as $key => $value) {
            if (is_numeric($key)) {
                $key = $value;
            }

            if (is_array($value) && count($value) == 1) {
                $class = array_keys($value)[0];
                $params = $value[$class];
                $arrData = $params[0] ?? '.';
                $transformer = $params[1] ?? null;
                $presenter = new $class(get_from_array($data, $arrData), $transformer);
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
        $transformerClass = $this->transformer;

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
}