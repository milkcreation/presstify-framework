<?php

namespace tiFy\Field\CheckboxCollection;

use tiFy\Field\FieldController;
use tiFy\Field\Checkbox\Checkbox;

class CheckboxCollection extends FieldController
{
    /**
     * Liste des attributs de configuration.
     * @var array $attrs {
     *      @var string $before Contenu placé avant le champ.
     *      @var string $after Contenu placé après le champ.
     *      @var string $name Attribut de configuration de la qualification de soumission du champ "name".
     *      @var array|Checkbox $items {
     *          Liste des attributs de configuration
     *          @see CheckboxItem
     *      }
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
            if ($attrs instanceof Checkbox) :
                $item = $attrs;
                $item->set('checked', $this->get('checked'));
                $item->set('name', $this->get('name'));
            else :
                $item = new CheckboxItem($name, $attrs, $this);
            endif;

            $items[] = $item;
        endforeach;

        $this->set('items', $items);
    }
}