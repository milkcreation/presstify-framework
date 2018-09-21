<?php

namespace tiFy\Metadata;

use tiFy\App\Dependency\AbstractAppDependency;
use tiFy\Metadata\Post;
use tiFy\Metadata\Term;
use tiFy\Metadata\User;
use tiFy\Metadata\UserOption;

class Metadata extends AbstractAppDependency
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton(Post::class)->build();
        $this->app->singleton(Term::class)->build();
        //@todo $this->appServiceShare(User::class, new User());
        //@todo $this->appServiceShare(UserOption::class, new UserOption());
    }
}