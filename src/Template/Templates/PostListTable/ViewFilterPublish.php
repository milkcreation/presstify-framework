<?php declare(strict_types=1);

namespace tiFy\Template\Templates\PostListTable;

use tiFy\Template\Templates\ListTable\ViewFilter;

class ViewFilterPublish extends ViewFilter
{
    /**
     * @inheritDoc
     */
    public function defaults(): array
    {
        $count = ($db = $this->factory->db())
            ? $db->where('post_status', 'publish')->count()
            : 0;

        return [
            'content'     => _n('Publié', 'Publiés', ($count > 1 ? 2 : 1), 'tify'),
            'count_items' => $count,
            'show_count'  => true,
            'query_args'  => ['post_status' => 'publish'],
            'current'     => $this->factory->request()->input('post_status') === 'publish'
        ];
    }
}