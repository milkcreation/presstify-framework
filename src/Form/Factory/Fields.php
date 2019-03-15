<?php

namespace tiFy\Form\Factory;

use tiFy\Contracts\Form\FactoryFields;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Kernel\Collection\Collection;

class Fields extends Collection implements FactoryFields
{
    use ResolverTrait;

    /**
     * Liste des éléments associés au formulaire.
     * @var FactoryField[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $fields Liste des champs associés au formulaire.
     * @param FormFactory $form Instance du contrôleur de formulaire.
     *
     * @return void
     */
    public function __construct($fields, FormFactory $form)
    {
        $this->form = $form;

        // Déclaration des champs.
        foreach ($fields as $slug => $attrs) :
            if (!is_null($slug)) :
                $this->items[$slug] = $this->resolve("factory.field", [$slug, $attrs, $this->form()]);

                app()->share("form.factory.field.{$this->form()->name()}.{$slug}", $this->items[$slug]);
            endif;
        endforeach;

        // Ordonnacement des champs.
        foreach ($this->byGroup() as $group) :
            $max = $group->max(function (FactoryField $field) { return $field->getPosition(); });
            $pad = 0;

            $group->each(function (FactoryField $field) use (&$pad, $max) {
                $number = 10000 * ($field->getGroup()+1);
                $position = $field->getPosition() ? : ++$pad+$max;

                return $field->setPosition(absint($number+$position));
            });
        endforeach;

        $this->items = $this->byPosition();
    }

    /**
     * @inheritdoc
     */
    public function byGroup()
    {
        return $this->collect()->groupBy(function (FactoryField $field) {
            return $field->getGroup();
        })->all();
    }

    /**
     * @inheritdoc
     */
    public function byPosition()
    {
        return $this->collect()->sortBy(function (FactoryField $field) {
            return $field->getPosition();
        })->all();
    }

    /**
     * @inheritdoc
     */
    public function hasGroup()
    {
        return !empty(
            $this->collect()->max(function (FactoryField $field) {
                return $field->getGroup();
            })
        );
    }
}