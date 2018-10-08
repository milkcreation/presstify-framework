<?php

namespace tiFy\Field\Text;

use tiFy\Field\AbstractFieldItem;

class Text extends AbstractFieldItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var array $attrs Liste des propriétés de la balise HTML.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var string $value Attribut de configuration de la valeur initiale de soumission du champ "value".
     *      @var array $viewer Liste des attributs de configuration de la classe des gabarits d'affichage.
     * }
     */
    protected $attributes = [
        'before' => '',
        'after'  => '',
        'attrs'  => [],
        'name'   => '',
        'value'  => '',
        'viewer' => []
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        if (!$this->get('attrs.type')) :
            $this->set('attrs.type', 'text');
        endif;
    }
}