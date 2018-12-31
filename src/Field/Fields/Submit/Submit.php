<?php

namespace tiFy\Field\Fields\Submit;

use tiFy\Field\FieldController;

class Submit extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Clé d'indice de la valeur de soumission du champ.
     *      @var string $value Valeur courante de soumission du champ.
     *      @var array $attrs Attributs HTML du champ.
     *      @var array $viewer Liste des attributs de configuration du controleur de gabarit d'affichage.
     * }
     */
    protected $attributes = [
        'before' => '',
        'after'  => '',
        'name'   => '',
        'value'  => '',
        'attrs'  => [],
        'viewer' => []
    ];

    /**
     * {@inheritdoc}
     */
    public function defaults()
    {
        return [
            'value' => __('Envoyer', 'tify')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $this->set('attrs.type', 'submit');
    }
}