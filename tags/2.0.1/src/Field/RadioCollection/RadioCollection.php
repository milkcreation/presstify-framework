<?php

namespace tiFy\Field\RadioCollection;

use tiFy\Field\AbstractFieldItem;
use tiFy\Field\Radio\Radio;

class RadioCollection extends AbstractFieldItem
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var null|string $checked Valeur de la selection.
     * }
     */
    protected $attributes = [
        'before'  => '',
        'after'   => '',
        'attrs'   => [],
        'name'    => '',
        'items'   => [],
        'checked' => null
    ];

    /**
     * {@inheritdoc}
     */
    public function parse($attrs = [])
    {
        parent::parse($attrs);

        $items = [];
        foreach ($this->get('items', []) as $name => $attrs) :
            if ($attrs instanceof Radio) :
                $item = $attrs;
                $item->set('checked', $this->get('checked'));
                $item->set('name', $this->get('name'));
            else :
                $item = new RadioItem($name, $attrs, $this);
            endif;

            $items[] = $item;
        endforeach;

        $this->set('items', $items);
    }
}