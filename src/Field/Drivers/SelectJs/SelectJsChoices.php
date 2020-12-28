<?php

declare(strict_types=1);

namespace tiFy\Field\Drivers\SelectJs;

use Illuminate\Support\Arr;
use tiFy\Contracts\Support\ParamsBag;
use tiFy\Field\Drivers\Select\SelectChoice;
use tiFy\Field\Drivers\Select\SelectChoiceInterface;
use tiFy\Field\Drivers\Select\SelectChoices;
use tiFy\Field\Drivers\SelectDriverInterface;
use WP_Query;

class SelectJsChoices extends SelectChoices implements SelectJsChoicesInterface
{
    /**
     * Instance du champ associé.
     * @var SelectDriverInterface
     */
    protected $field;

    /**
     * @param array|ParamsBag $items
     * @param mixed $selected Liste des éléments selectionnés
     */
    public function __construct($items, $selected = null)
    {
        if ($items instanceof ParamsBag) {
            $args = is_null($selected)
                ? $items->all()
                : array_merge(
                    ['in' => $selected, 'per_page' => -1],
                    $items->all()
                );

            $this->query($args);

            $this->selected = Arr::wrap($selected);
        } else {
            parent::__construct($items, $selected);
        }
    }

    /**
     * @inheritdoc
     */
    public function query(array $args): void
    {
        $args['post__in'] = $args['post__in'] ?? ($args['in'] ?? []);
        $args['post__not_in'] = $args['post__not_in'] ?? ($args['not_in'] ?? []);
        $args['posts_per_page'] = $args['posts_per_page'] ?? ($args['per_page'] ?? 20);
        $args['paged'] = $args['page'] ?? 1;
        if (!empty($args['term'])) {
            $args['s'] = $args['term'];
        }
        $args['post_type'] = $args['post_type'] ?? 'any';

        unset($args['in'], $args['not_in'], $args['per_page'], $args['page'], $args['term']);

        $items = [];
        $wp_query = new WP_Query($args);
        if ($wp_query->have_posts()) {
            while ($wp_query->have_posts()) {
                $wp_query->the_post();
                global $post;

                $items[] = ['value' => get_the_ID(), 'content' => get_the_title(), 'args' => ['post' => $post]];
            }
        }

        wp_reset_query();

        array_walk($items, [$this, 'walk']);
    }

    /**
     * Définition du controleur de champ associé.
     *
     * @param SelectDriverInterface $field
     *
     * @return static
     */
    public function setField(SelectDriverInterface $field): SelectJsChoicesInterface
    {
        if (!$this->field instanceof SelectDriverInterface) {
            $this->field = $field;
        }
        return $this;
    }

    /**
     * Définition du controleur d'élement.
     *
     * @param SelectChoiceInterface $item
     *
     * @return static
     */
    public function setItem(SelectChoiceInterface $item): SelectJsChoicesInterface
    {
        $item->set('picker', $this->field->view('picker-item', compact('item')));
        $item->set('selection', $this->field->view('selection-item', compact('item')));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function walker($items = [], $depth = 0, $parent = null): string
    {
        $output = "";
        foreach ($items as $item) {
            /** @var SelectChoiceInterface $item */
            if ($item->getParent() !== $parent) {
                continue;
            } else {
                $item->setDepth($depth)->parse()->setSelected($this->selected);

                $this->setItem($item);

                $output .= $item->tagOpen();
                $output .= $item->tagContent();
                $output .= $this->walker($items, ($depth + 1), $item->getName());
                $output .= $item->tagClose();
            }
        }
        return $output;
    }

    /**
     * @inheritdoc
     */
    public function walk($item, $name = null): SelectChoiceInterface
    {
        if (!$item instanceof SelectChoiceInterface) {
            $item = new SelectChoice((string)$name, $item);
        }
        return $this->items[] = $item;
    }
}