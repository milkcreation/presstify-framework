<?php

namespace tiFy\Components\Layout\ListTable\BulkAction;

use tiFy\Kernel\Item\ItemIteratorInterface;

interface BulkActionItemInterface extends ItemIteratorInterface
{
    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();
}