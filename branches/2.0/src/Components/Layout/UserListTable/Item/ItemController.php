<?php

namespace tiFy\Components\Layout\UserListTable\Item;

use tiFy\Components\Layout\ListTable\Item\ItemController as ListTableItemController;
use tiFy\Apps\Layout\LayoutInterface;

class ItemController extends ListTableItemController
{
    /**
     * CONSTRUCTEUR.
     *
     * @param \WP_User $user Liste des paramètres personnalisés.
     * @param LayoutInterface $app  Classe de rappel du controleur de l'application.
     *
     * @return void
     */
    public function __construct(\WP_User $user, LayoutInterface $app)
    {
        $attrs = $user->to_array();
        $attrs['roles'] = $user->roles;

        parent::__construct($attrs, $app);
    }
}