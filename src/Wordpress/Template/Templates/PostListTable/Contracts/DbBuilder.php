<?php declare(strict_types=1);

namespace tiFy\Wordpress\Template\Templates\PostListTable\Contracts;

use Corcel\Model\Builder\PostBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Support\ParamsBag;
use tiFy\Template\Templates\ListTable\Contracts\DbBuilder as BaseDbBuilder;

interface DbBuilder extends BaseDbBuilder
{
    /**
     * Retrouve la liste des variables de requête de récupération des éléments.
     *
     * @return ParamsBag
     */
    public function fetchWpQueryVars(): ParamsBag;

    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function query(): ?EloquentBuilder;
}