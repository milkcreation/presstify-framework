<?php

namespace tiFy\Forms\Buttons\Submit;

use tiFy\Field\Field;
use tiFy\Forms\Buttons\AbstractButtonController;
use tiFy\Partial\Partial;

class Submit extends AbstractButtonController
{
    /**
     * Nom de qualification du bouton
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
     * {@inheritdoc}
     */
    public function button()
    {
        $output = "";
        $output .= Field::Hidden(
            null,
            [
                'name'  => 'submit-' . $this->form()->getUid(),
                'value' => 'submit'
            ]
        );
        $output .= Field::Button(
            null,
            [
                'type' => 'submit',
                'attrs' => [
                    'id' => "submit-" . $this->form()->getUid(),
                    'class' => join(' ', $this->getHandlerClasses()),
                    'tabindex' => $this->form()->increasedTabIndex()
                ],
                'content' => $this->get('label', '')
            ]
        );

        return $output;
    }
}