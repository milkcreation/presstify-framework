<?php

namespace tiFy\Partial;

use Illuminate\Support\Arr;
use League\Plates\Engine;
use tiFy\Apps\Templates\TemplateBaseController;

class TemplateController extends TemplateBaseController
{
    /**
     * Affichage du contenu placé après le champ.
     *
     * @return void
     */
    public function after()
    {
        echo $this->get('after', '');
    }

    /**
     * Affichage des attributs HTML linéarisé.
     *
     * @return void
     */
    public function attrs()
    {
        echo $this->htmlAttrs($this->get('attrs', []));
    }

    /**
     * Affichage du contenu placé avant le champ.
     *
     * @return void
     */
    public function before()
    {
        echo $this->get('before', '');
    }

    /**
     * Vérifie si une variable peut-être appelé comme une fonction
     *
     * @param mixed $var Variable à tester.
     *
     * @return bool
     */
    public function isCallable($var)
    {
        if (is_string($var) && !preg_match('#\\\#', $var)) :
            return false;
        else :
            return is_callable($var);
        endif;
    }

    /**
     * Récupération de l'identifiant de qualification du controleur.
     *
     * @return string
     */
    public function getId()
    {
        return $this->getArg('id');
    }

    /**
     * Récupération de l'indice de la classe courante.
     *
     * @return int
     */
    public function getIndex()
    {
        return $this->getArg('index', 0);
    }
}