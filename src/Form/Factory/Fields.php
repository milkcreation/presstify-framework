<?php

namespace tiFy\Form\Factory;

use Illuminate\Support\Collection;
use tiFy\Contracts\Form\FactoryFields;
use tiFy\Contracts\Form\FactoryField;
use tiFy\Contracts\Form\FormFactory;
use tiFy\Form\Factory\ResolverTrait as FormFactoryResolver;

class Fields implements FactoryFields
{
    use FormFactoryResolver;

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
    public function __construct($fields = [], FormFactory $form)
    {
        $this->form = $form;

        // Déclaration des champs.
        foreach ($fields as $slug => $attrs) :
            if (!is_null($slug)) :
                $this->items[$slug] = app()->singleton(
                    "form.factory.field.{$this->form->name()}.{$slug}",
                    function ($slug, $attrs = []) {
                        return app()->resolve('form.factory.field', [$slug, $attrs, $this->form]);
                    }
                )->build([$slug, $attrs]);
            endif;
        endforeach;

        // Ordonnacement des champs.
        foreach ($this->byGroup() as $group) :
            $max = $group->max(function (FactoryField $field) { return $field->getPosition(); });
            $pad = 0;

            $group->each(function (FactoryField $field, $key) use (&$pad, $max) {
                $number = 1000 * ($field->getGroup() + 1);
                $order = $field->getPosition() ? : ++$pad+$max;

                return $field->setPosition(absint($number+$order));
            });
        endforeach;

        $this->events('fields.init', [&$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function byGroup()
    {
        return (new Collection($this->items))->groupBy(function (FactoryField $field) {
            return $field->getGroup();
        })->all();
    }

    /**
     * {@inheritdoc}
     */
    public function byOrder()
    {
        return (new Collection($this->items))->sortBy(function (FactoryField $field) {
            return $field->getPosition();
        })->all();
    }

    /**
     * {@inheritdoc}
     */
    public function get($slug)
    {
        return isset($this->items[$slug]) ? $this->items[$slug] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function hasGroup()
    {
        return !empty((new Collection($this->items))->max(function (FactoryField $field) {
            return $field->getGroup();
        }));
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     */
    /**
     * Initialisation de la liste des champs.
     *
     * @return void
     */
    private function _initFields()
    {

    }
}