<?php

namespace tiFy\Field\RadioCollection;

use Illuminate\Support\Arr;
use tiFy\Contracts\Field\RadioChoices as RadioChoicesContract;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Kernel\Collection\Collection;

class RadioChoices extends Collection implements RadioChoicesContract
{
    /**
     * Liste des éléments.
     * @var RadioChoice[]
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

        foreach($items as $key => $item) :
            $item = $this->wrap($key, $item);
            $item->setName($name);
        endforeach;

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

            $this->collect()->each(function (RadioChoice $item) use ($checked) {
                if (in_array($item->getValue(), $checked)) :
                    $item->setChecked();
                endif;
            });
        endif;

        if (!$this->collect()->first(function(RadioChoice $item) { return $item->isChecked(); })) :
            if ($first = $this->collect()->first()) :
                $first->setChecked();
            endif;
        endif;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function wrap($key, $item)
    {
        if (!$item instanceof RadioChoice) :
            $item = new RadioChoice($key, $item);
        endif;

        return $this->items[$key] = $item;
    }
}