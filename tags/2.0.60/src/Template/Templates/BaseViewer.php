<?php

namespace tiFy\Template\Templates;

use tiFy\Contracts\Template\TemplateFactory;
use tiFy\View\ViewController;

/**
 * Class BaseViewer
 * @package tiFy\Template\Templates
 *
 * @mixin TemplateFactory
 */
class BaseViewer extends ViewController
{
    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $template;

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
                [$this->template, $name],
                $arguments
            );
        endif;

        return null;
    }

    /**
     * @inheritdoc
     */
    public function boot()
    {
        $this->template = $this->engine->get('template');
    }
}