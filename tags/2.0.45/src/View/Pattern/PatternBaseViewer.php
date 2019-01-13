<?php

namespace tiFy\View\Pattern;

use tiFy\View\ViewPatternController;
use tiFy\View\ViewController;

/**
 * Class PatternBaseViewer
 * @package tiFy\View\Pattern
 *
 * @mixin ViewPatternController
 */
class PatternBaseViewer extends ViewController
{
    /**
     * Instance de la disposition.
     * @var ViewPatternController
     */
    protected $pattern;

    /**
     * Liste des méthodes heritées.
     * @var array
     */
    protected $mixins = [
        'label',
        'name',
        'param'
    ];

    /**
     * Appel des méthodes héritées du motif d'affichage associée.
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
}