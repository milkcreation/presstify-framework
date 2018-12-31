<?php

namespace tiFy\Field\Fields\Button;

use tiFy\Field\FieldController;

class Button extends FieldController
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
     *      @var string $content Contenu de la balise HTML.
     *      @var string $type Type de bouton. button par défaut.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'name'    => '',
        'value'   => '',
        'attrs'   => [],
        'viewer'  => [],
        'content' => '',
        'type'    => 'button',
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