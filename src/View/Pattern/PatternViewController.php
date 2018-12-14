<?php

namespace tiFy\View\Pattern;

use tiFy\Contracts\View\PatternController;
use tiFy\View\ViewController;

/**
 * Class PatternViewController
 * @package tiFy\View\Pattern
 *
 * @mixin \tiFy\View\Pattern\PatternController
 */
class PatternViewController extends ViewController
{
    /**
     * Instance de la disposition.
     * @var PatternController
     */
    protected $pattern;

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
                [$this->pattern, $name],
                $arguments
            );
        endif;

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->pattern = $this->engine->get('pattern');
    }

    /**
     * Récupération du nom de qualification de la vue.
     *
     * @return string
     */
    public function getName()
    {
        return $this->pattern->name();
    }

    /**
     * Récupération de la classe de rappel de gestion des paramètres.
     *
     * @return mixed
     */
    public function param($key, $default = null)
    {
        return $this->pattern->param($key, $default);
    }
}