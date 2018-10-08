<?php

namespace tiFy\Contracts\User;

use tiFy\Contracts\Kernel\ParametersBagInterface;

interface UserRoleItemController extends ParametersBagInterface
{
    /**
     * Récupération du nom de qualification.
     *
     * @return string
     */
    public function getName();
}