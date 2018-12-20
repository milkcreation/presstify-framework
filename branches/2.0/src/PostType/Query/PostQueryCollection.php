<?php

namespace tiFy\PostType\Query;

use tiFy\Contracts\PostType\PostQueryCollection as PostQueryCollectionContract;
use tiFy\Kernel\Collection\QueryCollection;
use WP_Query;

class PostQueryCollection extends QueryCollection implements PostQueryCollectionContract
{
    /**
     * Liste des éléments déclarés.
     * @var PostQueryItem[] $items
     */
    protected $items = [];

    /**
     * CONSTRUCTEUR.
     *
     * @param array|WP_Query $items
     *
     * @return void
     */
    public function __construct($items)
    {
        if ($items instanceof WP_Query) :
            if ($items->posts) :
                foreach($items->posts as $k => $post) :
                    $this->wrap($post);
                endforeach;
            endif;
        else :
            $this->items = $items;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getIds()
    {
        return $this->collect()->pluck('ID')->all();
    }

    /**
     * {@inheritdoc}
     */
    public function getTitles()
    {
        return $this->collect()->pluck('post_title')->all();
    }

    /**
     * {@inheritdoc}
     *
     * @param int $key Clé d'indice de l'élément.
     * @param \WP_Post $post
     *
     * @return PostQueryItem
     */
    public function wrap($key, $post)
    {
        return $this->items[$key] = new PostQueryItem($post);
    }
}