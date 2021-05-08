<?php declare(strict_types=1);

namespace tiFy\Wordpress\Template\Templates\UserListTable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Contracts\Template\{FactoryDb, FactoryBuilder};
use tiFy\Support\ParamsBag;
use tiFy\Template\Templates\ListTable\DbBuilder as BaseDbBuilder;
use tiFy\Wordpress\Template\Templates\UserListTable\Contracts\{Db, DbBuilder as DbBuilderContract};
use tiFy\Wordpress\Contracts\Database\UserBuilder;
use WP_User_Query;

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
        'user_login',
        'user_pass',
        'user_nicename',
        'user_email',
        'user_url',
        'user_registered',
        'user_activation_key',
        'user_status',
        'display_name',
        'spam',
        'deleted'
    ];

    /**
     * Instance de la requête courante en base de données.
     * @var UserBuilder|null
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
        } else {
            $this->parse();

            $query = new WP_User_Query($this->fetchWpUserQueryVars()->all());

            $total = $query->get_total();

            $items = $query->get_results();

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
    }

    /**
     * @inheritDoc
     */
    public function fetchWpUserQueryVars(): ParamsBag
    {
        $qv = new ParamsBag();

        if ($roles = $this->get('roles', [])) {
            $qv->set('role__in', $roles);
        }

        if ($number = $this->getPerPage()) {
            $qv->set('number', $this->getPerPage());
        }

        if ($paged = $this->getPage()) {
            $qv->set('offset', ($this->getPage()-1)*$this->getPerPage());
        }

        if ($order = $this->getOrder()) {
            $qv->set('order', $order);
        }

        if ($orderby = $this->getOrderBy()) {
            $qv->set('orderby', $orderby);
        }

        if ($search = $this->getSearch()) {
            $qv->set('search_columns', [
                'user_login',
                'user_email',
                'user_nicename',
                'display_name'
            ]);
            $qv->set('search', "*{$search}*");
        }

        return $qv;
    }

    /**
     * @inheritDoc
     */
    public function parse(): FactoryBuilder
    {
        parent::parse();

        return $this;
    }

    /**
     * {@inheritDoc}
     *
     * @return UserBuilder
     */
    public function query(): ?EloquentBuilder
    {
        return parent::query();
    }

    /**
     * {@inheritDoc}
     *
     * @return UserBuilder
     */
    public function querySearch(): EloquentBuilder
    {
        if ($term = $this->getSearch()) {
            $terms = is_string($term) ? explode(' ', $term) : $term;

            $terms = collect($terms)->map(function ($term) {
                return trim(str_replace('%', '', $term));
            })->filter()->map(function ($term) {
                return '%' . $term . '%';
            });

            if ($terms->isEmpty()) {
                return $this->query();
            }

            return $this->query()->where(function (EloquentBuilder $query) use ($terms) {
                $terms->each(function ($term) use ($query) {
                    /** @var UserBuilder $query */
                    $query->orWhere('user_login', 'like', $term)
                        ->orWhere('user_email', 'like', $term)
                        ->orWhere('user_nicename', 'like', $term)
                        ->orWhere('display_name', 'like', $term);
                });
            });
        }

        return $this->query();
    }

    /**
     * {@inheritDoc}
     *
     * @return UserBuilder
     */
    public function queryWhere(): EloquentBuilder
    {
        parent::queryWhere();

        if ($roles = $this->get('roles')) {
            if (is_string($roles)) {
                $roles = [$roles];
            }

            $this->query()->whereHas('meta', function (EloquentBuilder $query) use ($roles) {
                foreach($roles as $i => $role) {
                    if (!$i) {
                        $query->where('meta_key', $this->db()->getBlogPrefix() . 'capabilities')
                            ->where('meta_value', 'like', "%{$role}%");
                    } else {
                        $query->orWhere('meta_key', $this->db()->getBlogPrefix() . 'capabilities')
                            ->where('meta_value', 'like', "%{$role}%");
                    }
                }
            });
        }

        return $this->query;
    }
}