<?php

if (!function_exists('to_camel_case')){
    /**
     * Make given text as camelCase
     *
     * @param string $string
     * @param string $delimiter
     * @return string
     */
    function to_camel_case(string $string, $delimiter = '_') : string
    {
        if (empty($string)) return $string;

        $words = explode($delimiter, $string);

        return join("", array_map(function($word) {
            return ucfirst(strtolower($word));
        }, $words));
    }
}

if (!function_exists('get_from_array')) {
   function get_from_array($map, string $node)
    {
        if ($map === null) {
            return $map;
        }

        if (!is_array($map)) {
            return $map;
        }

        if (empty($node)) {
            return $map;
        }

        if ($node) {
            $terminate = false;
            $path = explode('.', $node);
            foreach ($path as $val) {
                if (!array_key_exists($val, $map)) {
                    $terminate = true;
                    break;
                }
                $map = &$map[$val];
            }
            if ($terminate) {
                return null;
            }
            return $map;
        }
        return null;
    }

}

if (!function_exists('is_collection')) {
    /**
     * Check given value is multidimensional array
     *
     * @param array $arr
     * @return bool
     */
    function is_collection($arr) : bool
    {
        if (!is_array($arr)) {
            return false;
        }
        $first = reset($arr);
        //$key = key($first);

        return isset($first) && is_array($first);
    }
}