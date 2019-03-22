<?php

namespace tiFy\Contracts\User;

use tiFy\Contracts\Support\ParamsBag;

interface RoleFactory extends ParamsBag
{
    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();
}