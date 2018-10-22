<?php

namespace tiFy\Form\Button\Submit;

use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\ButtonController;

class Submit extends ButtonController
{
    /**
     * CONSTRUCTEUR.
     *
     * @param array $attrs Liste des attributs de configuration.
     * @param FormFactory $form Instance du contrôleur de formulaire associé.
     *
     * @void
     */
    public function __construct($attrs = [], FormFactory $form)
    {
        parent::__construct('submit', $attrs, $form);
    }

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content'   => __('Envoyer', 'tify')
        ];
    }
}