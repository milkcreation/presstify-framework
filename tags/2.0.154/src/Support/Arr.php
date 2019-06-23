<?php declare(strict_types=1);

namespace tiFy\Support;

use Illuminate\Support\Arr as BaseArr;

class Arr extends BaseArr
{
    /**
     * Serialisation de données si nécessaire.
     * @see https://codex.wordpress.org/Function_Reference/maybe_serialize
     *
     * @param string|array|object $data.
     *
     * @return mixed
     */
    public static function serialize($data)
    {
        if (is_array($data) || is_object($data)) {
            $data = serialize($data);
        } elseif (is_serialized($data, false)) {
            $data = serialize($data);
        }

        return $data;
    }
}