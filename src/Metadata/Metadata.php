<?php

namespace tiFy\Metadata;

use tiFy\Apps\AppController;
use tiFy\Metadata\Post;
use tiFy\Metadata\Term;
use tiFy\Metadata\User;
use tiFy\Metadata\UserOption;

class Metadata extends AppController
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->appServiceAdd(Post::class, new Post());
        $this->appServiceAdd(Term::class, new Post());
        $this->appServiceAdd(User::class, new Post());
        $this->appServiceAdd(UserOption::class, new Post());
    }
}