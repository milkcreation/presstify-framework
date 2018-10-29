<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParametersBagIteratorInterface;

interface BulkActionItemInterface extends ParametersBagIteratorInterface
{
    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();
}