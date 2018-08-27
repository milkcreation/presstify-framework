<?php

namespace tiFy\Components\Field\Button;

use tiFy\Field\AbstractFieldItem;

class Button extends AbstractFieldItem
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
    public function parse($attrs = [])
    {
        $this->set('content', __('Envoyer', 'tify'));

        parent::parse($attrs);

        if (!$this->has('attrs.type')) :
            $this->set('attrs.type',  $this->get('type', 'button'));
        endif;
    }
}