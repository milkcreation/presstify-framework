<?php

namespace tiFy\Components\AdminView\ListTable\Column;

class ColumnItemCbController extends ColumnItemController
{
    /**
     * {@inheritdoc}
     */
    public function display($item)
    {
        return (($db = $this->view->getDb()) && ($primary = $db->getPrimary()) && isset($item->{$primary})) ? sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $primary, $item->{$primary}) : '';
    }
}