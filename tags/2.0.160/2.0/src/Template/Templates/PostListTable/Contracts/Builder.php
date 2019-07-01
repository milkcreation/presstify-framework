<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable\Contracts;

use Corcel\Model\Builder\PostBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use tiFy\Template\Templates\ListTable\Contracts\Builder as ListTableBuilder;

interface Builder extends ListTableBuilder
{
    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function query(): ?EloquentBuilder;
}