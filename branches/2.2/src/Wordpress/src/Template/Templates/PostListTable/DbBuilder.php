<?php declare(strict_types=1);

namespace tiFy\Wordpress\Template\Templates\PostListTable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Contracts\Template\FactoryDb;
use tiFy\Support\ParamsBag;
use tiFy\Template\Templates\ListTable\DbBuilder as BaseDbBuilder;
use tiFy\Wordpress\Template\Templates\PostListTable\Contracts\{Db, DbBuilder as DbBuilderContract};
use tiFy\Wordpress\Contracts\Database\PostBuilder;
use WP_Query;

class DbBuilder extends BaseDbBuilder implements DbBuilderContract
{
    /**
     * Clé primaire.
     * @var string|null
     */
    protected $keyName = 'ID';

    /**
     * Liste des colonnes.
     * @var string[]|null
     */
    protected $columns = [
        'ID',
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'guid',
        'menu_order',
        'post_type',
        'post_mime_type',
        'comment_count',
    ];

    /**
     * Instance de la requête courante en base de données.
     * @var PostBuilder|null
     */
    protected $query;

    /**
     * {@inheritDoc}
     *
     * @return Db
     */
    public function db(): ?FactoryDb
    {
        return parent::db();
    }

    /**
     * @inheritDoc
     */
    public function fetchItems(): \tiFy\Template\Templates\ListTable\Contracts\DbBuilder
    {
        if ($this->db()) {
            return parent::fetchItems();
        }
        $this->parse();

        $args = $this->fetchWpQueryVars()->all();

        $query = new WP_Query($args);

        $total = (int)$query->found_posts;

        $items = $query->posts;

        if ($total < $this->getPerPage()) {
            $this->setPage(1);
        }

        $this->factory->items()->set($items);

        if ($count = count($items)) {
            $this->factory->pagination()
                ->setCount($count)
                ->setCurrentPage($this->getPage())
                ->setPerPage($this->getPerPage())
                ->setLastPage((int)ceil($total / $this->getPerPage()))
                ->setTotal($total)
                ->parse();
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function fetchWpQueryVars(): ParamsBag
    {
        $qv = new ParamsBag();

        $qv->set('post_type', ($post_type = $this->get('post_type')) ? $post_type : 'any');

        if ($post_status = $this->get('post_status')) {
            $qv->set('post_status', $post_status);
        }

        if ($meta_query = $this->get('meta_query')) {
            $qv->set('meta_query', $meta_query);
        }

        if ($tax_query = $this->get('tax_query')) {
            $qv->set('tax_query', $tax_query);
        }

        if ($number = $this->getPerPage()) {
            $qv->set('posts_per_page', $this->getPerPage());
        }

        if ($paged = $this->getPage()) {
            $qv->set('paged', $this->getPage());
        }

        if ($order = $this->getOrder()) {
            $qv->set('order', $order);
        }

        if ($orderby = $this->getOrderBy()) {
            $qv->set('orderby', $orderby);
        }

        if ($search = $this->getSearch()) {
            $qv->set('s', $search);
        }

        return $qv;
    }

    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function query(): ?EloquentBuilder
    {
        return parent::query();
    }

    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function queryLimit(): EloquentBuilder
    {
        return parent::queryLimit();
    }

    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function queryOrder(): EloquentBuilder
    {
        return parent::queryOrder();
    }

    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function querySearch(): EloquentBuilder
    {
        if ($term = $this->getSearch()) {
            $terms = is_string($term) ? explode(' ', $term) : $term;

            $terms = collect($terms)->map(
                function ($term) {
                    return trim(str_replace('%', '', $term));
                }
            )->filter()->map(
                function ($term) {
                    return '%' . $term . '%';
                }
            );

            if ($terms->isEmpty()) {
                return $this->query();
            }

            return $this->query()->where(
                function (EloquentBuilder $query) use ($terms) {
                    $terms->each(
                        function ($term) use ($query) {
                            /** @var PostBuilder $query */
                            $query->orWhere('post_title', 'like', $term)
                                ->orWhere('post_excerpt', 'like', $term)
                                ->orWhere('post_content', 'like', $term);
                        }
                    );
                }
            );
        }

        return $this->query();
    }

    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function queryWhere(): EloquentBuilder
    {
        foreach ($this->all() as $k => $v) {
            if ($this->hasColumn($k)) {
                is_array($v) ? $this->query()->whereIn($k, $v) : $this->query()->where($k, $v);
            }
        }

        foreach ($this->get('meta', []) as $k => $v) {
            if (!is_null($v)) {
                $this->query()->whereHas(
                    'meta',
                    function (EloquentBuilder $query) use ($k, $v) {
                        $query->where('meta_key', $k)->whereIn('meta_value', is_array($v) ? $v : [$v]);
                    }
                );
            }
        }

        foreach ($this->get('tax', []) as $taxonomy => $terms) {
            if (!is_null($terms)) {
                $this->query()
                    ->where('taxonomy', $taxonomy)
                    ->whereHas(
                        'taxonomies',
                        function (EloquentBuilder $query) use ($taxonomy, $terms) {
                            $query->whereHas(
                                'term',
                                function (EloquentBuilder $query) use ($terms) {
                                    $query->whereIn('slug', is_array($terms) ? $terms : [$terms]);
                                }
                            );
                        }
                    );
            }
        }

        return $this->query();
    }
}