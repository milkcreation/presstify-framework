<?php

namespace tiFy\View\Pattern\ListTable\Contracts;

use tiFy\Contracts\Kernel\ParamsBag;

interface BulkActionsItem extends ParamsBag
{
    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();
}