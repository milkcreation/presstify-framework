<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable\Contracts;

use Corcel\Model\Builder\PostBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Template\Templates\ListTable\Contracts\DbBuilder as ListTableDbBuilder;

interface DbBuilder extends ListTableDbBuilder
{
    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function query(): ?EloquentBuilder;
}