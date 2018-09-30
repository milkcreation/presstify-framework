<?php

namespace tiFy\Layout\Display;

use tiFy\Contracts\Layout\LayoutDisplayInterface;
use tiFy\Contracts\Layout\LayoutDisplayParamsInterface;
use tiFy\Kernel\Templates\TemplateController;

/**
 * Class LayoutDisplayView
 *
 */
class ViewController extends TemplateController
{
    /**
     * Instance de la disposition.
     * @var LayoutDisplayInterface
     */
    protected $layout;

    /**
     * Liste des méthodes héritées.
     * @var array
     */
    protected $mixins = [];

    /**
     * Translation d'appel des méthodes de l'application associée.
     *
     * @param string $name Nom de la méthode à appeler.
     * @param array $arguments Liste des variables passées en argument.
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, $this->mixins)) :
            return call_user_func_array(
                [$this->layout, $name],
                $arguments
            );
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->layout = $this->engine->get('layout');
    }

    /**
     * Récupération d'un intitulé.
     *
     * @param string $key Clé d'index de l'intitulé.
     * @param string $default Valeur de retour par défaut.
     *
     * @return string
     */
    public function getLabel($key, $default = '')
    {
        return $this->layout->label($key, $default);
    }

    /**
     * Récupération du nom de qualification de la vue.
     *
     * @return string
     */
    public function getName()
    {
        return $this->layout->name();
    }

    /**
     * Récupération de la classe de rappel de gestion des paramètres.
     *
     * @return LayoutDisplayParamsInterface
     */
    public function param($key = null, $default = null)
    {
        return $this->layout->param($key, $default);
    }
}