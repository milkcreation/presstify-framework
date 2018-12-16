<?php

namespace tiFy\Field\Button;

use tiFy\Field\FieldController;

class Button extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var string $type Type de bouton. button par défaut.
     *      @var string $content Contenu de la balise HTML.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'name'    => '',
        'value'   => '',
        'type'    => 'button',
        'content' => ''
    ];

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'content' => __('Envoyer', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->has('attrs.type')) :
            $this->set('attrs.type',  $this->get('type', 'button'));
        endif;
    }
}