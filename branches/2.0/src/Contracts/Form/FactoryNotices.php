<?php

namespace tiFy\Contracts\Form;

use tiFy\Contracts\Kernel\ParamsBagInterface as tiFyParamsBag;
use tiFy\Contracts\Kernel\Notices as tiFyNotices;
use tiFy\Contracts\Form\FormResolver;

interface FactoryNotices extends FormResolver, tiFyNotices
{
    /**
     * Récupération d'un paramètre ou de l'intance du contrôleur des paramètres.
     *
     * @param null|string $key Clé d'indexe du paramètres à récupérer. Syntaxe à points permise.
     * @param mixed $default Valeur de retour par défaut.
     *
     * @return mixed|tiFyParamsBag
     */
    public function params($key = null, $default = null);
}