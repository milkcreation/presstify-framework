<?php declare(strict_types=1);

namespace tiFy\Template\Templates\UserListTable\Contracts;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Template\Templates\ListTable\Contracts\DbBuilder as ListTableDbBuilder;
use tiFy\Wordpress\Contracts\Database\UserBuilder;

interface DbBuilder extends ListTableDbBuilder
{
    /**
     * {@inheritDoc}
     *
     * @return UserBuilder
     */
    public function query(): ?EloquentBuilder;
}