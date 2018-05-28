<?php

namespace tiFy\Kernel\Templates;

use Illuminate\Support\Arr;
use League\Plates\Template\Template as LeagueTemplate;
use tiFy\Kernel\Tools;

class Template extends LeagueTemplate
{
    /**
     * Récupération d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed
     */
    public function get($key, $default = '')
    {
        return Arr::get($this->data, $key, $default);
    }

    /**
     * Vérification d'existance d'un attribut de configuration.
     *
     * @param string $key Clé d'indexe de l'attribut. Syntaxe à point permise.
     *
     * @return bool
     */
    public function has($key)
    {
        return Arr::has($this->data, $key);
    }

    /**
     * Linératisation d'une liste d'attributs HTML.
     *
     * @return string
     */
    public function htmlAttrs($attrs)
    {
        return Tools::Html()->parseAttrs($attrs, true);
    }

    /**
     *
     */
    public function partial($name, $datas = [])
    {
        return 'tutu';
    }
}