<?php declare(strict_types=1);

namespace tiFy\Template\Templates\UserListTable;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Contracts\Template\{FactoryDb, FactoryBuilder};
use tiFy\Template\Templates\ListTable\Builder as ListTableBuilder;
use tiFy\Template\Templates\UserListTable\Contracts\{Db, Builder as BuilderContract};
use tiFy\Wordpress\Contracts\Database\UserBuilder;

class Builder extends ListTableBuilder implements BuilderContract
{
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
                        $query->where('meta_key', $this->db()->getConnection()->getTablePrefix() . 'capabilities')
                            ->where('meta_value', 'like', "%{$role}%");
                    } else {
                        $query->orWhere('meta_key', $this->db()->getConnection()->getTablePrefix() . 'capabilities')
                            ->where('meta_value', 'like', "%{$role}%");
                    }
                }
            });
        }

        return $this->query;
    }
}