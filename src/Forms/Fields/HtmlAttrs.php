<?php
namespace tiFy\Forms\Fields;

class HtmlAttrs
{
    /**
     * Cartographie de la valeur d'un attributs de balise HTML
     *
     * @param $attr
     * @param $value
     *
     * @return string
     */
    public static function getValue($attr, $value)
    {
        switch ($attr) :
            case 'autocomplete' :
                return ($value && ($value !== 'off')) ? "autocomplete=\"{$value}\"" : "autocomplete=\"off\"";
                break;
            case 'readonly' :
                return ($value && ($value !== 'off')) ? 'readonly' : '';
                break;
            case 'disabled' :
                return ($value && ($value !== 'off')) ? 'disabled' : '';
                break;
            case 'onpaste' :
                return ($value && ($value === 'off')) ? 'return false;' : '';
                break;
            default:
                return "{$attr}=\"{$value}\"";
        endswitch;
    }
}