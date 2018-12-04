<?php

namespace tiFy\Form\Addon\Mailer;

use tiFy\Contracts\Form\FormFactory;
use tiFy\Contracts\Metabox\MetaboxFactory;
use tiFy\Metabox\MetaboxWpOptionsController;
use tiFy\Form\Factory\ResolverTrait;

class MailerOptions extends MetaboxWpOptionsController
{
    use ResolverTrait;

    /**
     * Liste des noms d'enregistement des options.
     *
     * @var array
     */
    protected $optionNames = [];

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct(FormFactory $form)
    {
        $this->form = $form;
    }

    /**
     * Instanciation du contrôleur.
     *
     * @param MetaboxFactory $item Instance de l'élément.
     * @param array $attrs Liste des variables passées en arguments.
     *
     * @return void
     */
    public function __invoke(MetaboxFactory $item, $args = [])
    {
        return parent::__construct($item, $args);
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->optionNames = $this->form()->addon('mailer')->get('option_names', []);
    }

    /**
     * {@inheritdoc}
     */
    public function load($wp_screen)
    {
        add_action(
            'admin_enqueue_scripts',
            function () {
                field('repeater')->enqueue_scripts();
                field('toggle-switch')->enqueue_scripts();
            }
        );
    }
}