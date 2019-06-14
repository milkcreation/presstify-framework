<?php

namespace tiFy\Taxonomy\Query;

use tiFy\Contracts\Taxonomy\TermQueryInterface;
use tiFy\Taxonomy\Query\TermQueryCollection;
use tiFy\Taxonomy\Query\TermQueryItem;

class TermQuery implements TermQueryInterface
{
    /**
     * Taxonomie Wordpress du controleur
     * @var string
     */
    protected $objectName = '';

    /**
     * Controleur de données d'un élément
     * @var string
     */
    protected $itemController = TermQueryItem::class;

    /**
     * Controleur de données d'une liste d'éléments
     * @var string
     */
    protected $collectionController = TermQueryCollection::class;

    /**
     * {@inheritdoc}
     */
    public function getCollection($query_args = [])
    {
        if (!$query_args['taxonomy'] = $this->getObjectName()) :
            return [];
        endif;

        $terms = \get_terms($query_args);

        if ($terms && !is_wp_error($terms)) :
            $items = array_map([$this, 'getItem'], $terms);
        else :
            $items = [];
        endif;

        return $this->resolveCollection($items);
    }

    /**
     * {@inheritdoc}
     */
    public function getItem($id = null)
    {
        if (!$id) :
            $term = get_queried_object();
        elseif (is_numeric($id) && $id > 0) :
            if ((!$term = \get_term($id)) || is_wp_error($term)) :
                return null;
            endif;
        elseif (is_string($id)) :
            return $this->getItemBy(null, $id);
        else :
            $term = $id;
        endif;

        if (!$term instanceof \WP_Term) :
            return null;
        endif;

        if ($term->taxonomy !== $this->getObjectName()) :
            return null;
        endif;

        $alias = 'taxonomy.query.term.' . $term->term_id;;
        if (!app()->has($alias)) :
            app()->singleton(
                $alias,
                function() use ($term) {
                    return $this->resolveItem($term);
                }
            );
        endif;

        return app()->resolve($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemBy($key = 'slug', $value)
    {
        switch ($key) :
            default :
                if (($term = \get_term_by($key, $value, $this->getObjectName())) && !is_wp_error($term)) :
                    return $this->getItem($term);
                endif;
                break;
        endswitch;

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return $this->objectName;
    }

    /**
     * {@inheritdoc}
     */
    public function resolveCollection($items)
    {
        $concrete = $this->collectionController;

        return new $concrete($items);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveItem($wp_term)
    {
        $concrete = $this->itemController;

        return new $concrete($wp_term);
    }
}