<?php

namespace tiFy\Form\Field\Recaptcha;

use tiFy\Contracts\Api\Recaptcha as ApiRecaptcha;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Form\FieldController;

class Recaptcha extends FieldController
{
    /**
     * Liste des attributs de support.
     * @var array
     */
    protected $supports = ['label', 'request', 'wrapper'];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->events()->listen(
            'validation.check.field.recaptcha',
            [$this, 'onValidationCheckField']
        );
    }

    /**
     * Contrôle d'intégrité des champs.
     *
     * @param array $errors Liste des erreurs de soumission de formulaire.
     * @param FieldItemController $field Instance du controleur de champ associé.
     *
     * @return void
     */
    public function onValidationCheckField(&$errors, FactoryField $field)
    {
        /** @var ApiRecaptcha $recaptcha */
        $recaptcha = app('api.recaptcha');

        if (!$recaptcha->validation()->isSuccess()) :
            $errors[] = [
                'message' => __('La saisie de la protection antispam est incorrecte.', 'tify'),
                'type'    => 'field',
                'slug'    => $field->getSlug(),
                'order'   => $field->getOrder(),
            ];
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function content()
    {
        return field(
            'recaptcha',
            [
                'name'  => $this->field()->getName(),
                'attrs' => [
                    'id' => preg_replace('/-/', '_', sanitize_key($this->form()->name()))
                ]
            ]
        );
    }
}