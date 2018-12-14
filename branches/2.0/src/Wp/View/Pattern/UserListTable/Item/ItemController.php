<?php

namespace tiFy\Layout\Share\WpUserListTable\Item;

use tiFy\Layout\Share\ListTable\Item\ItemController as ShareListTableItemController;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;

class ItemController extends ShareListTableItemController
{
    /**
     * CONSTRUCTEUR.
     *
     * @param \WP_User $user Liste des paramètres personnalisés.
     * @param ListTableInterface $layout Instance du controleur de disposition associé.
     *
     * @return void
     */
    public function __construct(\WP_User $user, ListTableInterface $layout)
    {
        $attrs = $user->to_array();
        $attrs['roles'] = $user->roles;

        parent::__construct($attrs, $layout);
    }
}