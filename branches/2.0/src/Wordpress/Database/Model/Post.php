<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\Post as CorcelPost;
use Illuminate\Database\Eloquent\Builder;
use tiFy\Support\ParamsBag;
use tiFy\Wordpress\Contracts\PostBuilder;

/**
 * @method static PostBuilder hasMeta(string|array $meta_key, mixed|null $value, string $operator = '=')
 * @method static PostBuilder hasMetaLike(string $key, string $value)
 */
class Post extends CorcelPost implements PostBuilder
{
    /**
     * Liste des colonnes de la table.
     * @var array
     */
    protected $columns;

    /**
     * @var Builder
     */
    protected $clauseQuery;

    /**
     * @var array
     */
    protected $builtInClasses = [
        \Corcel\Model\Comment::class => \Corcel\Model\Meta\CommentMeta::class,
        Post::class                  => Postmeta::class,
        \Corcel\Model\Term::class    => \Corcel\Model\Meta\TermMeta::class,
        \Corcel\Model\User::class    => \Corcel\Model\Meta\UserMeta::class,
    ];

    /**
     * Récupération de la liste des colonnes de la table.
     *
     * @return array
     */
    public function getColumns(): array
    {
        if (is_null($this->columns)) {
            $this->columns = $this->getConnection()->getSchemaBuilder()->getColumnListing($this->getTable())
                ?: [];
        }

        return $this->columns;
    }

    /**
     * Vérification d'une colonne dans la table.
     *
     * @param string $name Nom de qualification de la colonne.
     *
     * @return bool
     */
    public function hasColumn(string $name): bool
    {
        return in_array($name, $this->getColumns());
    }

    /**
     * Condition de requête de limitation dun nombre d'éléments.
     *
     * @param int $page_num Numéro de la page courante.
     * @param int $per_page Nombre d'élément par page.
     * @param Builder $query Instance de la requête à traiter.
     *
     * @return Builder
     */
    public function clauseLimit(int $page_num = 1, int $per_page = 20, ?Builder $query = null): Builder
    {
        $query = $query ?: $this->clauseQuery();

        return $query->forPage($page_num > 0 ? $page_num : 1, $per_page);
    }

    /**
     * Condition de requête d'ordonnacement des éléments.
     *
     * @param string|null $orderby Nom de qualification de la colonne d'ordonnacement.
     * @param string $order Sens de trie ASC|DESC.
     * @param Builder $query Instance de la requête à traiter.
     *
     * @return Builder
     */
    public function clauseOrder(?string $orderby = '', string $order = 'ASC', ?Builder $query = null): Builder
    {
        $query = $query ?: $this->clauseQuery();

        if (!$orderby) {
            $orderby = $this->getKeyName();
        }

        return $query->orderBy($orderby, $order);
    }

    /**
     * Récupération de la requête courante.
     *
     * @return Builder
     */
    public function clauseQuery(): Builder
    {
        if (is_null($this->clauseQuery)) {
            $this->clauseQuery = $this->query();
        }

        return $this->clauseQuery;
    }

    /**
     * Condition de requête de filtrage selon des attributs relatif à la table.
     *
     * @param array $args Liste des attributs de filtrage.
     * @param Builder $query Instance de la requête à traiter.
     *
     * @return Builder
     */
    public function clauseWhere(array $args = [], ?Builder $query = null): Builder
    {
        $query = $query ?: $this->clauseQuery();

        foreach ($args as $k => $v) {
            if ($this->hasColumn($k)) {
                is_array($v) ? $query->whereIn($k, $v) : $query->where($k, $v);
            }
        }

        return $query;
    }

    /**
     * Condition de requête de filtrage selon des attributs relatif aux métadonnées.
     *
     * @param array $args Liste des attributs de filtrage.
     * @param Builder $query Instance de la requête à traiter.
     *
     * @return Builder
     */
    public function clauseWhereMeta(array $args = [], ?Builder $query = null): Builder
    {
        $query = $query ?: $this->clauseQuery();

        foreach ($args as $k => $v) {
            if (!is_null($v)) {
                $query->whereHas('meta', function (Builder $query) use ($k, $v) {
                    $query->where('meta_key', $k);
                    is_array($v) ? $query->whereIn('meta_value', $v) : $query->where('meta_value', $v);
                });
            }
        }

        return $query;
    }

    /**
     * Condition de requête de filtrage selon des attributs relatif aux termes de taxonomies.
     *
     * @param array $args Liste des attributs de filtrage.
     * @param Builder $query Instance de la requête à traiter.
     *
     * @return Builder
     */
    public function clauseWhereTaxonomy(array $args = [], ?Builder $query = null): Builder
    {
        $query = $query ?: $this->clauseQuery();

        foreach ($args as $taxonomy => $terms) {
            if (!is_null($terms)) {
                $query
                    ->where('taxonomy', $taxonomy)
                    ->whereHas('taxonomies', function (Builder $query) use ($taxonomy, $terms) {
                        $query->whereHas('term', function (Builder $query) use ($terms) {
                            $query->whereIn('slug', is_array($terms) ? $terms : [$terms]);
                        });
                    });
            }
        }

        return $query;
    }

    /**
     * Récupération de la requête basée sur une liste d'arguments.
     *
     * @param array $args Liste des arguments de requêtes.
     * @param Builder|null $query Instance de la requête à traiter.
     *
     * @return Builder
     */
    public function queryFromArgs(array $args = [], ?Builder $query = null): Builder
    {
        $query = $query ?: $this->clauseQuery();

        $p = ParamsBag::createFromAttrs($args);

        $query = $this->clauseLimit($p->pull('paged', 1), $p->pull('per_page', 20), $query);
        $query = $this->clauseOrder($p->pull('orderby', ''), $p->pull('order', 'ASC'), $query);
        $query = $this->clauseWhereMeta($p->pull('meta', []), $query);
        $query = $this->clauseWhereTaxonomy($p->pull('tax', []), $query);
        $query = $this->clauseWhere($p->all(), $query);

        return $query;
    }
}