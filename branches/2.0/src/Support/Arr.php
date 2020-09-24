<?php declare(strict_types=1);

namespace tiFy\Support;

use Illuminate\Support\Arr as BaseArr;
use tiFy\Validation\Validator as v;

class Arr extends BaseArr
{
    /**
     * Serialisation de données si nécessaire.
     * @see https://codex.wordpress.org/Function_Reference/maybe_serialize
     *
     * @param string|array|object $data .
     *
     * @return mixed
     */
    public static function serialize($data)
    {
        if (is_array($data) || is_object($data)) {
            $data = serialize($data);
        } elseif (v::serialized(false)->validate($data)) {
            $data = serialize($data);
        }

        return $data;
    }

    /**
     *
     */
    public static function stripslashes($data)
    {
        if (is_array($data)) {
            foreach ($data as $index => $item) {
                $data[$index] = static::stripslashes($item);
            }
        } elseif (is_object($data)) {
            $object_vars = get_object_vars($data);

            foreach ($object_vars as $property_name => $property_value) {
                $data->$property_name = static::stripslashes($property_value);
            }
        } elseif (is_string($data)) {
            $data = stripslashes($data);
        }

        return $data;
    }
}