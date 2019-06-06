<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable\Contracts;

use Corcel\Model\Builder\PostBuilder;
use Illuminate\Database\Eloquent\Builder;
use tiFy\Template\Templates\ListTable\Contracts\QueryBuilder as ListTableQueryBuilder;

interface QueryBuilder extends ListTableQueryBuilder
{
    /**
     * {@inheritDoc}
     *
     * @return PostBuilder
     */
    public function query(): ?Builder;
}