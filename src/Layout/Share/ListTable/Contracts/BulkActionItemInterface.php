<?php

namespace tiFy\Layout\Share\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface BulkActionItemInterface extends ParamsBag
{
    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();
}