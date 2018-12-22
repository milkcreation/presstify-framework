<?php

namespace tiFy\Field\CheckboxCollection;

use Illuminate\Support\Arr;
use tiFy\Contracts\Field\CheckboxChoices as CheckboxChoicesContract;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Kernel\Collection\Collection;

class CheckboxChoices extends Collection implements CheckboxChoicesContract
{
    /**
     * Liste des éléments.
     * @var CheckboxChoice[]
     */
    protected $items = [];

    /**
     * Instance du controleur d'affichage des gabarits.
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste des éléments
     * @param string $name Nom de soumission de l'élément dans la requête de traitement.
     * @param ViewEngine $viewer Instance du controleur d'affichage des gabarits.
     * @param mixed $checked Liste des éléments selectionnés.
     */
    public function __construct($items, $name, ViewEngine $viewer, $checked = null)
    {
        $this->viewer = $viewer;

        array_walk($items, function($item, $key) use ($name) {
            $this->wrap($item, $key)->setName($name);
        });

        $this->setChecked($checked);
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return (string)$this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->viewer->make('choices', ['items' => $this->items]);
    }

    /**
     * {@inheritdoc}
     */
    public function setChecked($checked = null)
    {
        if (!is_null($checked)) :
            $checked = Arr::wrap($checked);

            $this->collect()->each(function (CheckboxChoice $item) use ($checked) {
                if (in_array($item->getValue(), $checked)) :
                    $item->setChecked();
                endif;
            });
        endif;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function wrap($item, $key = null)
    {
        if (!$item instanceof CheckboxChoice) :
            $item = new CheckboxChoice($key, $item);
        endif;

        return $this->items[$key] = $item;
    }
}