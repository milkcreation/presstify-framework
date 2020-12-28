<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\CheckboxCollection;

use Illuminate\Support\Collection;
use tiFy\Field\Drivers\CheckboxCollectionDriverInterface;
use tiFy\Support\Arr;

class CheckboxWalker implements CheckboxWalkerInterface
{
    /**
     * Indicateur d'initialisation.
     * @var bool
     */
    private $built = false;

    /**
     * Instance du champ associé.
     * @var CheckboxCollectionDriverInterface
     */
    protected $field;

    /**
     * Liste des éléments.
     * @var CheckboxChoice[]
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array $items Liste des éléments
     */
    public function __construct(array $items)
    {
        array_walk(
            $items,
            function ($item, $key) {
                $this->setItem($item, $key);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * @inheritDoc
     */
    public function build(): CheckboxWalkerInterface
    {
        if (!$this->built) {
            if ($this->exists()) {
                foreach ($this->items as $item) {
                    $item->setWalker($this)->build()->setNameAttr($this->field->getName());
                }

                $this->registerChecked();
            }

            $this->built = true;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function exists(): bool
    {
        return !!$this->items;
    }

    /**
     * @inheritDoc
     */
    public function registerChecked(): CheckboxWalkerInterface
    {
        $checked = $this->field->getValue();
        $collect = new Collection($this->items);

        if (!is_null($checked)) {
            $checked = Arr::wrap($checked);

            $collect->each(
                function (CheckboxChoice $item) use ($checked) {
                    if (in_array($item->getCheckbox()->get('checked'), $checked)) {
                        $item->setChecked();
                    }
                }
            );
        } elseif ($default = $this->field->get('default')) {
            if ($default === true) {
                if (!$collect->first(function (CheckboxChoice $item) { return $item->isChecked(); })) {
                    if ($first = $collect->first()) {
                        $first->setChecked();
                    }
                }
            } else {
                $default = Arr::wrap($default);

                $collect->each(
                    function (CheckboxChoice $item) use ($default) {
                        if (in_array($item->getCheckbox()->get('checked'), $default)) {
                            $item->setChecked();
                        }
                    }
                );
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->field->view('choices', ['items' => $this->items]);
    }

    /**
     * @inheritDoc
     */
    public function setField(CheckboxCollectionDriverInterface $field): CheckboxWalkerInterface
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setItem($item, $key = null): CheckboxChoiceInterface
    {
        if (!$item instanceof CheckboxChoiceInterface) {
            $item = new CheckboxChoice($key, $item);
        }

        return $this->items[$key] = $item;
    }
}