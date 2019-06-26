<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Contracts\Template\FactoryDb;
use tiFy\Template\Templates\ListTable\Builder as ListTableBuilder;
use tiFy\Template\Templates\PostListTable\Contracts\{Db, Builder as BuilderContract};
use tiFy\Wordpress\Contracts\Database\PostBuilder;

class Builder extends ListTableBuilder implements BuilderContract
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
    public function queryWhere(): EloquentBuilder
    {
        parent::queryWhere();

        foreach ($this->all() as $k => $v) {
            if ($this->hasColumn($k)) {
                is_array($v) ? $this->query()->whereIn($k, $v) : $this->query()->where($k, $v);
            }
        }

        foreach($this->get('meta', []) as $k => $v) {
            if (!is_null($v)) {
                $this->query()->whereHas('meta', function (EloquentBuilder $query) use ($k, $v) {
                    $query->where('meta_key', $k)->whereIn('meta_value', $v);
                });
            }
        }

        foreach ($this->get('tax', []) as $taxonomy => $terms) {
            if (!is_null($terms)) {
                $this->query()
                    ->where('taxonomy', $taxonomy)
                    ->whereHas('taxonomies', function (EloquentBuilder $query) use ($taxonomy, $terms) {
                        $query->whereHas('term', function (EloquentBuilder $query) use ($terms) {
                            $query->whereIn('slug', is_array($terms) ? $terms : [$terms]);
                        });
                    });
            }
        }

        return $this->query();
    }
}