<?php

namespace tiFy\PostType\Query;

use tiFy\Contracts\PostType\PostQueryInterface;
use tiFy\PostType\Query\PostQueryCollection;
use tiFy\PostType\Query\PostQueryItem;

class PostQuery implements PostQueryInterface
{
    /**
     * Type de post Wordpress du controleur.
     * @var string|array
     */
    protected $objectName = 'any';

    /**
     * Controleur de données d'un élément.
     * @var string
     */
    protected $itemController = PostQueryItem::class;

    /**
     * Controleur de données d'une liste d'éléments.
     * @var string
     */
    protected $collectionController = PostQueryCollection::class;

    /**
     * {@inheritdoc}
     */
    public function getCollection($query_args = [])
    {
        if (!$query_args['post_type'] = $this->objectName) :
            return [];
        endif;

        if (!isset($query_args['posts_per_page'])) :
            $query_args['posts_per_page'] = -1;
        endif;

        $wp_query = new \WP_Query;
        $posts = $wp_query->query($query_args);

        if ($posts) :
            $items =  array_map([$this, 'getItem'], $posts);
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
            $post = get_the_ID();
        elseif (is_numeric($id) && $id > 0) :
            $post = $id;
        elseif (is_string($id)) :
            return $this->getItemBy(null, $id);
        else :
            $post = $id;
        endif;

        if (!$post = \get_post($post)) :
            return null;
        endif;

        if (!$post instanceof \WP_Post) :
            return null;
        endif;

        if (($post->post_type !== 'any') && !in_array($post->post_type, (array) $this->getObjectName())) :
            return null;
        endif;

        $alias = 'post_type.query.post.' . $post->ID;
        if (!app()->has($alias)) :
            app()->singleton(
                $alias,
                function() use ($post) {
                    return $this->resolveItem($post);
                }
            );
        endif;

        return app()->resolve($alias);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemBy($key = 'name', $value)
    {
        $args = [
            'post_type'      => 'any',
            'posts_per_page' => 1
        ];

        switch ($key) :
            default :
            case 'post_name' :
            case 'name' :
                $args['name'] = $value;
                break;
        endswitch;

        $wp_query = new \WP_Query;
        $posts = $wp_query->query($args);
        if ($wp_query->found_posts) :
            return $this->getItem(reset($posts));
        endif;

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
    public function resolveItem($wp_post)
    {
        $concrete = $this->itemController;

        return new $concrete($wp_post);
    }
}

