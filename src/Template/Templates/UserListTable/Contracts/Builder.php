<?php declare(strict_types=1);

namespace tiFy\Template\Templates\UserListTable\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Template\Templates\ListTable\Contracts\Builder as ListTableBuilder;
use tiFy\Wordpress\Contracts\Database\UserBuilder;

interface Builder extends ListTableBuilder
{
    /**
     * {@inheritDoc}
     *
     * @return UserBuilder
     */
    public function query(): ?EloquentBuilder;
}