<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Contracts\Template\TemplateFactory;
use tiFy\Template\Templates\ListTable\ListTable as BaseListTable;
use tiFy\Template\Templates\PostListTable\Contracts\PostListTable as PostListTableContract;

class PostListTable extends BaseListTable implements PostListTableContract
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        PostListTableServiceProvider::class,
    ];

    /**
     * {@inheritDoc}
     *
     * @return PostListTableContract
     */
    public function prepare(): TemplateFactory
    {
        return parent::prepare();
    }
}