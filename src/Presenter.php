<?php declare(strict_types = 1);

namespace Nahid\Presento;


abstract class Presenter
{
    /**
     * @var string|null
     */
    protected $transformer = null;

    /**
     * @var array|mixed
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $generatedData = [];

    /**
     * @var null
     */
    protected $default = null;

    /**
     * @var array
     */
    protected $presentScheme;

    /**
     * @var bool
     * @since v1.1
     */
    protected $isProcessed = false;

    public function __construct($data = null, string $transformer = null)
    {
        $this->presentScheme = $this->present();
        $this->data = $this->init($data);

        $this->transformer = $this->transformer();
        if (!is_null($transformer)) {
            $this->transformer = $transformer;
        }
    }

    public function __invoke()
    {
        return $this->get();
    }

    public function __toString() : string
    {
        return json_encode($this->generatedData);
    }

    /**
     * @param array $present
     * @return $this
     */
    public function setPresent(array $present)
    {
        $this->presentScheme = $present;
        return $this;
    }


    /**
     * @param string $transformer
     * @return $this
     */
    public function setTransformer(string $transformer)
    {
        $this->transformer = $transformer;
        return $this;
    }

    /**
     * @return array
     * @since v1.1
     */
    public function getPresent() : array
    {
        return $this->presentScheme;
    }

    /**
     * @return string|null
     * @since v1.1
     */
    public function getTransformer()
    {
        return $this->transformer;
    }

    public function setDefault($value) : self
    {
        $this->default = $value;

        return $this;
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
     * @param $data
     * @return mixed
     * @since v1.1
     */
    public function init($data)
    {
        return $this->convert($data);
    }

    /**
     * @param $data
     * @return mixed
     *
     * @deprecated 1.1.0
     */
    public function convert($data)
    {
        return $data;
    }

    /**
     *
     * @param $data
     * @return mixed
     */
    public function map($data)
    {
        return $data;
    }

    /**
     * handle data based on presented data
     *
     * @return array
     */
    public function handle()
    {
        $this->isProcessed = true;

        if (is_collection($this->data)) {
            $generatedData = [];
            foreach ($this->data  as $property => $data) {
                $generatedData[$property] = $this->handleDefault($this->map($data));
            }
            return $generatedData;
        }

        return $this->handleDefault($this->map($this->data));
    }

    protected function handleDefault($data)
    {
        if (!blank($data)) {
            return $this->transform($this->process($data));
        }

        if (is_array($this->default) && count($this->default) > 0) {
            $this->presentScheme = $this->default;
            return $this->transform($this->process($data));
        }

        return $this->default;
    }

    /**
     * process data based presented data model
     *
     * @param array $data
     * @return array
     */
    public function process($data)
    {
        $present = $this->presentScheme;
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
                $arrData = array_shift($params) ?? '.';
                $transformer = array_shift($params);
                $args = [get_from_array($data, $arrData), $transformer] + $params;

                $presenter = new $class(... $args);
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
    protected function transform($data)
    {
        if (!is_array($data)) {
            return $data;
        }

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
        return json_encode($this->get());
    }

    /**
     * get full set of data as array
     *
     */
    public function get()
    {
        if (!$this->isProcessed) {
            $this->generatedData = $this->handle();
        }

        return $this->generatedData;
    }
}