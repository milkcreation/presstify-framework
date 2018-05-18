<?php

namespace tiFy\Components\Form\Buttons\Submit;

use tiFy\Field\Field;
use tiFy\Form\Buttons\AbstractButtonController;
use tiFy\Partial\Partial;

class Submit extends AbstractButtonController
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