<?php

namespace tiFy\Contracts\User;

use tiFy\Contracts\Kernel\ParamsBag;

interface UserRoleItemController extends ParamsBag
{
    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();
}