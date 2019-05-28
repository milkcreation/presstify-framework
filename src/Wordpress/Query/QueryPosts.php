<?php declare(strict_types=1);

namespace tiFy\Wordpress\Query;

use tiFy\Wordpress\Contracts\QueryPosts as QueryPostsContract;
use tiFy\Support\Collection;
use WP_Post;
use WP_Query;

class QueryPosts extends Collection implements QueryPostsContract
{
    /**
     * Instance de la requête Wordpress de récupération des posts.
     * @var WP_Query
     */
    protected $wp_query;

    /**
     * CONSTRUCTEUR.
     *
     * @param WP_Query $wp_query Requête Wordpress de récupération de post.
     *
     * @return void
     */
    public function __construct(WP_Query $wp_query)
    {
        $this->wp_query = $wp_query;

        $this->set($this->wp_query->posts);
    }

    /**
     * @inheritdoc
     */
    public static function createFromArgs($args = []): QueryPostsContract
    {
        return new static(new WP_Query($args));
    }

    /**
     * @inheritdoc
     */
    public static function createFromGlobals(): QueryPostsContract
    {
        global $wp_query;

        return new static($wp_query);
    }

    /**
     * @inheritdoc
     */
    public static function createFromIds(array $ids, $post_types = null): QueryPostsContract
    {
        if (is_null($post_types)) :
            $post_types = array_keys(get_post_types());
        endif;

        return new static(new WP_Query(['post__in' => $ids, 'post_type' => $post_types, 'posts_per_page' => -1]));
    }

    /**
     * @inheritdoc
     */
    public function getIds(): array
    {
        return $this->pluck('ID');
    }

    /**
     * @inheritdoc
     */
    public function getTitles(): array
    {
        return $this->pluck('post_title');
    }

    /**
     * {@inheritdoc}
     *
     * @param WP_Post $item Objet post Wordpress.
     *
     * @return void
     */
    public function walk($item, $key = null)
    {
        $this->items[$key] = new QueryPost($item);
    }

    /**
     * @inheritdoc
     */
    public function WpQuery(): WP_Query
    {
        return $this->wp_query;
    }
}