<?php

namespace tiFy\Field\Fields\SelectJs;

use tiFy\Contracts\Kernel\ParamsBag;
use tiFy\Contracts\View\ViewEngine;
use tiFy\Field\Fields\Select\SelectChoice;
use tiFy\Field\Fields\Select\SelectChoices;
use WP_Query;

class SelectJsChoices extends SelectChoices
{
    /**
     * Instance du controleur de gestion des gabarits d'affichage.
     * @var ViewEngine
     */
    protected $viewer;

    /**
     * CONSTRUCTEUR.
     *
     * @param array|ParamsBag $items
     * @param ViewEngine $viewer
     * @param mixed $selected Liste des éléments selectionnés
     */
    public function __construct($items, ViewEngine $viewer, $selected = null)
    {
        $this->viewer = $viewer;

        if ($items instanceof ParamsBag) :
            $args = is_null($selected)
                ? $items->all()
                : array_merge(
                    ['in' => $selected, 'per_page' => -1],
                    $items->all()
                );

            $items = $this->query($args);

            array_walk($items, [$this, 'wrap']);
            $this->setSelected($selected);
        else :
            parent::__construct($items, $selected);
        endif;
    }

    /**
     * Requête de récupération des éléments.
     *
     * @param array $args Arguments de requête de récupération des éléments.
     *
     * @return array
     */
    public function query($args)
    {
        $args['post__in'] = $args['post__in'] ?? ($args['in'] ?? []);
        $args['post__not_in'] = $args['post__not_in'] ?? ($args['not_in'] ?? []);
        $args['posts_per_page'] = $args['posts_per_page'] ?? ($args['per_page'] ?? 20);
        $args['paged'] = $args['page'] ?? 1;
        if (!empty($args['term'])) :
            $args['s'] = $args['term'];
        endif;
        $args['post_type'] = $args['post_type'] ?? 'any';

        unset($args['in'], $args['not_in'], $args['per_page'], $args['page'], $args['term']);

        $items = [];
        $wp_query = new WP_Query($args);
        if ($wp_query->have_posts()) :
            while ($wp_query->have_posts()) : $wp_query->the_post();
                global $post;

                $items[] = ['value' => get_the_ID(), 'content' => get_the_title(), 'args' => ['post' => $post]];
            endwhile;
        endif;

        wp_reset_query();

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function wrap($item, $name = null)
    {
        if (!$item instanceof SelectChoice) :
            $item = new SelectChoice($name, $item);
            $args = $item->all();

            $item->set('picker',  (string)$this->viewer->make('picker-item', $args));
            $item->set('selection', (string)$this->viewer->make('selection-item', $args));
        endif;

        return $this->items[] = $item;
    }
}