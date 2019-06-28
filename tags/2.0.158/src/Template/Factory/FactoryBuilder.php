<?php declare(strict_types=1);

namespace tiFy\Template\Factory;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Contracts\Template\{FactoryBuilder as FactoryBuilderContract, FactoryDb, TemplateFactory};
use tiFy\Support\ParamsBag;

class FactoryBuilder extends ParamsBag implements FactoryBuilderContract
{
    use FactoryAwareTrait;

    /**
     * Instance du gabarit d'affichage.
     * @var TemplateFactory
     */
    protected $factory;

    /**
     * Liste des colonnes de la table de base de données.
     * @var string[]|null
     */
    protected $columns;

    /**
     * Sens d'ordonnancement des éléments.
     * @var string
     */
    protected $order = '';

    /**
     * Colonne d'ordonnancement des éléments.
     * @var string
     */
    protected $orderby = '';

    /**
     * Numéro de la page d'affichage courante.
     * @var int
     */
    protected $page = 0;

    /**
     * Nombre d'éléments affichés par page.
     * @var int|null
     */
    protected $perPage = 0;

    /**
     * Instance de la requête courante en base de données.
     * @var EloquentBuilder|null
     */
    protected $query;

    /**
     * @inheritDoc
     */
    public function db(): ?FactoryDb
    {
        return $this->factory->db();
    }

    /**
     * @inheritDoc
     */
    public function getColumns(): array
    {
        if (is_null($this->columns)) {
            $this->columns = $this->db()->getConnection()->getSchemaBuilder()->getColumnListing($this->db()->getTable())
                ?: [];
        }

        return $this->columns;
    }

    /**
     * @inheritDoc
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * @inheritDoc
     */
    public function getOrderBy(): string
    {
        return $this->orderby ?: $this->db()->getKeyName();
    }

    /**
     * @inheritDoc
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @inheritDoc
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @inheritDoc
     */
    public function hasColumn(string $name): bool
    {
        return in_array($name, $this->getColumns());
    }

    /**
     * @inheritDoc
     */
    public function parse(): FactoryBuilderContract
    {
        parent::parse();

        $this->set($this->factory->request()->all());

        $this
            ->setPerPage($this->get('per_page', 20))
            ->setPage((int)$this->get('paged', 1))
            ->setOrder($this->get('order', 'ASC'))
            ->setOrderBy($this->get('orderby', ''));

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function query(): ?EloquentBuilder
    {
        if (is_null($this->query)) {
            $this->query = $this->db() ? $this->db()::query() : null;
        }

        return $this->query;
    }

    /**
     * @inheritDoc
     */
    public function queryLimit(): EloquentBuilder
    {
        return $this->query()->forPage($this->getPage(), $this->getPerPage());
    }

    /**
     * @inheritDoc
     */
    public function queryOrder(): EloquentBuilder
    {
        return $this->query()->orderBy($this->getOrderBy(), $this->getOrder());
    }

    /**
     * @inheritDoc
     */
    public function queryWhere(): EloquentBuilder
    {
        foreach ($this->all() as $k => $v) {
            if ($this->hasColumn($k)) {
                is_array($v) ? $this->query()->whereIn($k, $v) : $this->query()->where($k, $v);
            }
        }

        return $this->query();
    }

    /**
     * @inheritDoc
     */
    public function remove($keys): FactoryBuilderContract
    {
        $this->forget($keys);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function resetQuery(): FactoryBuilderContract
    {
        $this->query = null;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOrder(string $order): FactoryBuilderContract
    {
        $order = strtoupper($order ?: 'ASC');
        $this->order = in_array($order, ['ASC', 'DESC']) ? $order : 'ASC';

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setOrderBy(string $orderby): FactoryBuilderContract
    {
        $this->orderby = $orderby;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPage(int $page): FactoryBuilderContract
    {
        $this->page = $page ? $page : 1;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setPerPage(int $per_page): FactoryBuilderContract
    {
        $this->perPage = $per_page > 0 ? $per_page : 0;

        return $this;
    }
}