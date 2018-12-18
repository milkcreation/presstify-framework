<?php

namespace tiFy\Field\CheckboxCollection;

use Illuminate\Support\Arr;
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
                if (($checked = Arr::wrap($this->get('checked', []))) && in_array($item->getValue(), $checked)) :
                    $item->push('attrs', 'checked');
                endif;
                $item->set('attrs.name', $this->getName());
            else :
                $item = new CheckboxItem($name, $attrs, $this);
            endif;

            $items[] = $item;
        endforeach;

        $this->set('items', $items);
    }

    /**
     * Traitement de l'attribut de configuration de la clé d'indexe de soumission du champ "name".
     *
     * @return void
     */
    protected function parseName()
    {
        if ($name = $this->get('name')) :
            $this->set('attrs.name', "{$name}[]");
        endif;
    }
}