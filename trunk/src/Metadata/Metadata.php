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
        $this->appServiceShare(Post::class, new Post());
        $this->appServiceShare(Term::class, new Post());
        $this->appServiceShare(User::class, new Post());
        $this->appServiceShare(UserOption::class, new Post());
    }
}