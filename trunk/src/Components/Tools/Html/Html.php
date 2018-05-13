<?php

namespace tiFy\Components\Tools\Html;

class Html
{
    /**
     * Traitement de la liste des attributs HTML.
     *
     * @param array $attrs Liste des attributs HTML.
     * @param bool $linearized Activation de la linéarisation.
     *
     * @return string
     */
    public function parseAttrs($attrs = [], $linearized = true)
    {
        $html_attrs = [];
        foreach ($attrs as $k => $v) :
            if (is_array($v)) :
                $v = rawurlencode(json_encode($v));
            endif;
            if (is_numeric($k)) :
                $html_attrs[]= "{$v}";
            else :
                $html_attrs[]= "{$k}=\"{$v}\"";
            endif;
        endforeach;

        return $linearized ? implode(' ', $html_attrs) : $html_attrs;
    }
}