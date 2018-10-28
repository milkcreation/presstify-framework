<?php

namespace tiFy\Kernel\Parameters;

class Parameters
{
    /**
     * Récupération de paramètres déclarés de manières linéaires.
     *
     * @param string $_args Liste des arguments linérarisés.
     *
     * @return array
     */
    public function extract($_args)
    {
        preg_match_all('#([a-zA-Z0-9_]+)|\[([a-zA-Z0-9_,]+)\]#', $_args, $matches);

        $args = $matches[0];

        foreach($args as &$arg) :
            if (preg_match("#^\[(.*)\]$#", $arg, $array)) :
                $arg = explode(',', $array[1]);
            endif;
        endforeach;

        return $args;
    }
}