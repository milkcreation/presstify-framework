<?php

namespace tiFy\Layout\Share\WpUserListTable\Item;

use tiFy\Layout\Share\ListTable\Item\ItemCollectionController as ShareListTableItemCollectionController;

class ItemCollectionController extends ShareListTableItemCollectionController
{
    /**
     * {@inheritdoc}
     */
    public function query($query_args = [])
    {
        if (!$db = $this->layout->db()) :
            return;
        endif;

        $query = new \WP_User_Query($query_args);
        if ($items = $query->get_results()) :
            foreach ($items as $item) :
                $this->items[] = $this->layout->resolve('item', [$item, $this->layout]);
            endforeach;
        endif;

        $this->total = $query->get_total();
    }
}