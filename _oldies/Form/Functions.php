<?php

namespace tiFy\Form\Controller;

class Functions
{


    /**
     * BACKUP
     * @param $data
     * @return string
     */
    /* = Génération d'une chaine de caractère encodée = */
    public static function hash($data)
    {
        return wp_hash($data);
    }

    /* = = */
    public static function referer()
    {
        $current_domain = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'];

        wp_unslash($_SERVER['REQUEST_URI']);
    }

    /* = = */
    public static function base64Decode($data, $unserialize = true)
    {
        if (!is_string($data)) {
            return $data;
        }

        $_data = $data;
        if (self::isBase64($_data)) {
            $_data = base64_decode($data, true);
        }

        if ($unserialize) {
            $_data = maybe_unserialize($_data);
        }

        return $_data;
    }

    /* = = */
    public static function base64Encode($data)
    {
        if (!is_serialized($data)) {
            $data = maybe_serialize($data);
        }

        return base64_encode($data);
    }

    /* = = */
    private static function is_base64($str)
    {
        if (!preg_match('~[^0-9a-zA-Z+/=]~', $str)) :
            $check = str_split(base64_decode($str));
            $x = 0;
            foreach ($check as $char) :
                if (ord($char) > 126) :
                    $x++;
                endif;
            endforeach;
            if ($x / count($check) * 100 < 30) {
                return true;
            }
        endif;

        return false;
    }
}