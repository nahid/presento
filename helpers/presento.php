<?php declare(strict_types = 1);

if (!function_exists('to_studly_case')) {
    /**
     * Make given text as camelCase
     *
     * @param string $string
     * @return string
     */
    function to_studly_case(string $string) : string
    {
        if (empty($string)) return $string;

        $words = str_replace(['-', '_', '.'], ' ', $string);
        $words = ucwords($words);

        return str_replace(' ', '', $words);
    }
}

if (!function_exists('to_camel_case')) {
    /**
     * Make given text as camelCase
     *
     * @param string $string
     * @return string
     */
    function to_camel_case(string $string) : string
    {
        return lcfirst(to_studly_case($string));
    }
}

if (!function_exists('get_from_array')) {
    /**
     * get data from an array traversing by the given 'path'
     *
     * @param $map
     * @param string $node
     * @param string $nodeTraveler
     * @return mixed|null
     */
    function get_from_array($map, string $node, $nodeTraveler = '.')
    {
        if ($map === null || !is_array($map) || empty($node) || $node == $nodeTraveler) {
            return $map;
        }

        $path = explode($nodeTraveler, $node);

        foreach ($path as $val) {
            if (!is_array($map)) {
                return null;
            }

            if (!array_key_exists($val, $map)) {
                return null;
            }

            $map = &$map[$val];
        }

        return $map;
    }
}

if (!function_exists('is_collection')) {
    /**
     * Check given value is multidimensional array
     *
     * @param mixed $array
     * @return bool
     */
    function is_collection($array) : bool
    {
        if (!is_array($array)) return false;

        return array_keys($array) === range(0, count($array) - 1);
    }
}


if (!function_exists('blank')) {
    /**
     * Check if the given value is null or empty
     *
     * @param $value
     * @return bool
     */
    function blank($value): bool
    {
        if (is_null($value)) {
            return true;
        }

        if (is_string($value)) {
            return trim($value) === '';
        }

        if (is_numeric($value) || is_bool($value)) {
            return false;
        }

        if ($value instanceof Countable) {
            return count($value) === 0;
        }

        return empty($value);
    }
}