<?php declare(strict_types=1);

namespace tiFy\Wordpress\Template\Templates\UserListTable\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Support\ParamsBag;
use tiFy\Template\Templates\ListTable\Contracts\DbBuilder as BaseDbBuilder;
use tiFy\Wordpress\Contracts\Database\UserBuilder;

interface DbBuilder extends BaseDbBuilder
{
    /**
     * Retrouve la liste des variables de requête de récupération des éléments.
     *
     * @return ParamsBag
     */
    public function fetchWpUserQueryVars(): ParamsBag;

    /**
     * {@inheritDoc}
     *
     * @return UserBuilder
     */
    public function query(): ?EloquentBuilder;
}