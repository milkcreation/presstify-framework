<?php

namespace tiFy\User;

use tiFy\App\Container\AppServiceProvider;
use tiFy\User\Metadata\Metadata;
use tiFy\User\Metadata\Option as MetaOption;
use tiFy\User\Role\Role;
use tiFy\User\Session\SessionManager;
use tiFy\User\Session\SessionStore;
use tiFy\User\SignIn\SignIn;
use tiFy\User\SignUp\SignUpController;
use tiFy\User\SignUp\SignUpManager;
use tiFy\User\User;

class UserServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton('user', function () { return new User(); })->build();

        $this->app->singleton('user.metadata', function () { return new Metadata(); })->build();

        $this->app->singleton('user.option', function () { return new MetaOption(); })->build();

        $this->app->singleton('user.role', function () { return new Role(); })->build();

        $this->app->singleton('user.session', function () { return new SessionManager(); })->build();
        $this->app->bind(
            'user.session.store',
            function ($name, $attrs = []) {
                return new SessionStore($name, $attrs);
            }
        );

        $this->app->singleton('user.signin', function () { return new SignIn(); })->build();

        $this->app->singleton('user.signup', function () { return new SignUpManager(); })->build();
        $this->app->bind(
            'user.signup.item',
            function ($name, $attrs = []) {
                return new SignUpController($name, $attrs);
            }
        );
    }
}