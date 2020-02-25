<?php declare(strict_types = 1);

namespace Nahid\Presento;


use Nahid\Presento\Exceptions\BadPropertyTransformerMethodException;

abstract class Transformer
{
    protected $generatedData = [];
    /**
     * @var null | string
     */
    protected $propertyMethodTransform = 'to_studly_case';
    private $data = [];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->transform();
    }

    public function __invoke() : array
    {
        return $this->getData();
    }

    /**
     * transform given data with desired methods
     *
     * @return void
     */
    public function transform()
    {
        foreach ($this->data as $key => $value) {
            $this->generatedData[$key] = $this->callPropertyFunction($key, $value);
        }
    }

    /**
     * check then current property need to be processed
     *
     * @param string $property
     * @return bool
     */
    protected function isPropertyNeedProcess(string $property) : bool
    {
        $method = $this->getPropertyFunction($property);

        return method_exists($this, $method);
    }

    /**
     * get property guessed function name
     *
     * @param string $property
     * @return string
     * @throws BadPropertyTransformerMethodException
     */
    protected function getPropertyFunction(string $property) : string
    {
        return sprintf('get%sProperty', $this->propertyMethodTransform($property));
    }

    /**
     * @param $property
     * @return mixed
     * @throws BadPropertyTransformerMethodException
     */
    protected function propertyMethodTransform($property)
    {
        if (!$this->propertyMethodTransform) {
            return $property;
        }

        if(function_exists($this->propertyMethodTransform)) {
            return call_user_func($this->propertyMethodTransform, $property);
        }

        throw new BadPropertyTransformerMethodException($this->propertyMethodTransform);
    }

    /**
     * call property function if exists
     *
     * @param string $property
     * @param $value
     * @return mixed
     */
    protected function callPropertyFunction(string $property, $value)
    {
        if ($this->isPropertyNeedProcess($property)) {
            return call_user_func_array([$this, $this->getPropertyFunction($property)], [$value]);
        }

        return $value;
    }

    /**
     * get property value from data
     *
     * @param string $property
     * @return array|mixed|null
     */
    public function getProperty(string $property)
    {
        return get_from_array($this->data, $property);
    }

    /**
     * get full set of data
     *
     * @return array
     */
    public function getData() : array
    {
        return $this->generatedData;
    }

}
