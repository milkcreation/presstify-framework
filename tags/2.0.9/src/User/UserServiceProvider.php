<?php

namespace tiFy\User;

use tiFy\App\Container\AppServiceProvider;
use tiFy\User\Metadata\Metadata as UserMetadata;
use tiFy\User\Metadata\Option as UserOption;
use tiFy\User\Role\Role as UserRole;
use tiFy\User\Session\Session as UserSession;
use tiFy\User\SignIn\SignIn as UserSignIn;
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

        $this->app->singleton('user.metadata', function () { return new UserMetadata(); })->build();

        $this->app->singleton('user.option', function () { return new UserOption(); })->build();

        $this->app->singleton('user.role', function () { return new UserRole(); })->build();

        $this->app->singleton('user.session', function () { return new UserSession(); })->build();

        $this->app->singleton('user.signin', function () { return new UserSignIn(); })->build();

        $this->app->singleton('user.signup', function () { return new SignUpManager(); })->build();
        $this->app->bind(
            'user.signup.item',
            function ($name, $attrs = []) {
                return new SignUpController($name, $attrs);
            }
        );
    }
}