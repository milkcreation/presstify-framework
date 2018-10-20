<?php

namespace tiFy\Form\Button\Submit;

use tiFy\Form\ButtonController;

class Submit extends ButtonController
{
    /**
     * Nom de qualification du bouton.
     * @var string
     */
    protected $name;

    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->attributes['label'] = __('Envoyer', 'tify');
        $this->attributes['order'] = 1;
    }

    /**
     * Reundu d'affichage du bouton.
     *
     * @return string
     */
    public function render()
    {
        $output = "";

        $output .= Field::Hidden(
            [
                'name'  => 'submit-' . $this->getForm()->getUid(),
                'value' => 'submit',
            ]
        );

        $output .= Field::Button(
            [
                'type'    => 'submit',
                'attrs'   => $this->getHtmlAttrs(),
                'content' => $this->get('label', ''),
            ]
        );

        return $output;
    }
}