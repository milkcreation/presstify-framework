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
            $items = array_map([$this, 'get'], $terms);
        else :
            $items = [];
        endif;

        $controller = $this->getCollectionController();

        return new $controller($items);
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectionController()
    {
        return $this->collectionController;
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

        $name = 'tify.query.term.' . $term->term_id;
        if (! $this->appServiceHas($name)) :
            $controller = $this->getItemController();
            $this->appServiceShare($name, new $controller($term));
        endif;

        return $this->appServiceGet($name);
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
    public function getItemController()
    {
        return $this->itemController;
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectName()
    {
        return (string) $this->objectName;
    }
}