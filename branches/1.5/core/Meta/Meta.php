<?php

namespace tiFy\Core\Meta;

use tiFy\App\Traits\App as TraitsApp;

class Meta
{
    use TraitsApp;

    /**
     * Liste des classe de rappel
     */
    public static $Factory  = [];

    /**
     * CONSTRUCTEUR
     *
     * @return void
     */
    public function __construct()
    {
        $Factory['post'] = new Post;
        $Factory['term'] = new Term;
        $Factory['user'] = new User;
        $Factory['user_option'] = new UserOption;
    }
}