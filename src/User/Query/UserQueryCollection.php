<?php

namespace tiFy\User\Query;

use Illuminate\Support\Collection;
use tiFy\Contracts\User\UserQueryCollection as UserQueryCollectionContract;
use tiFy\Contracts\User\UserQueryItem;

class UserQueryCollection extends Collection implements UserQueryCollectionContract
{
    /**
     * CONSTRUCTEUR
     *
     * @param UserQueryItem[] $items
     *
     * @return void
     */
    public function __construct($items = [])
    {
        parent::__construct($items);
    }
}