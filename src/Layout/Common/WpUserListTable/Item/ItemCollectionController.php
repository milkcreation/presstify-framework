<?php

namespace tiFy\Components\Layout\UserListTable\Item;

use tiFy\Components\Layout\ListTable\Item\ItemCollectionController as ListTableItemCollectionController;
use tiFy\Components\Layout\ListTable\Item\ItemInterface;

class ItemCollectionController extends ListTableItemCollectionController
{
    /**
     * {@inheritdoc}
     */
    public function query($query_args = [])
    {
        if (!$db = $this->app->db()) :
            return;
        endif;

        $query = new \WP_User_Query($query_args);
        if ($items = $query->get_results()) :
            foreach ($items as $item) :
                $this->items[] = $this->app->resolve(ItemInterface::class, [$item]);
            endforeach;
        endif;

        $this->total = $query->get_total();
    }
}