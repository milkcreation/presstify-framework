<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use Illuminate\Database\Eloquent\Builder;
use tiFy\Contracts\Template\{FactoryDb, FactoryQueryBuilder};
use tiFy\Template\Templates\ListTable\QueryBuilder as ListTableQueryBuilder;
use tiFy\Template\Templates\PostListTable\Contracts\{Db, QueryBuilder as QueryBuilderContract};
use tiFy\Wordpress\Contracts\PostBuilder;

class QueryBuilder extends ListTableQueryBuilder implements QueryBuilderContract
{
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
    public function parse(): FactoryQueryBuilder
    {
        parent::parse();

        if ($post_status = $this->get('post_status')) {
            $query_args['post_status'] = $post_status;
        }

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function query(): ?Builder
    {
        return parent::query();
    }

    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function whereClause(): Builder
    {
        parent::whereClause();

        foreach($this->get('meta', []) as $key => $value) {
            if (!is_null($value)) {
                $this->query()->whereHas('meta', function (Builder $query) use ($key, $value) {
                    $query->where('meta_key', $key)->whereIn('meta_value', $value);
                });
            }
        }

        return $this->query;
    }
}