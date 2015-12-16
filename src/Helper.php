<?php namespace Tarsana\IO;

use Tarsana\IO\Exceptions\HelperException;


class Helper {

    /**
     * Converts the provided data to an array recursively.
     * 
     * @param  mixed   $data
     * @param  boolean $literalToArray
     * @param  boolean $level
     * @return array|null
     *
     * @throws Tarsana\IO\Exceptions\HelperException
     */
    public static function toArray($data, $literalToArray = true, $level = 0)
    {
        if ($level > 200) {
            throw new HelperException('Error while converting data to array');
        }
        $level += 1;
        if (is_array($data)) {
            $array = [];
            foreach ($data as $key => $value) {
                if (! is_numeric($key)) {
                    // if the key is not numeric, it can be the name of an attribute
                    // of an object, so the next lines are required to decode 
                    // this name when it's a private or protected attribute
                    $key = var_export($key, true);
                    $key = substr($key, 1, strlen($key) - 2);
                    $key = str_replace('\' . "\0" . \'*\' . "\0" . \'', '', $key);
                }
                $value = static::toArray($value, false, $level);
                if (null !== $value) {
                    $array[$key] = $value; 
                }
            }
            return $array;
        }
        if (is_object($data)) {
            if (is_callable($data)) {
                return null;
            }
            return static::toArray((array) $data, false, $level);
        }
        if($literalToArray) {
            return (array) $data;
        }
        return $data;
    }
}
