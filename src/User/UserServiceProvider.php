<?php

namespace tiFy\User;

use tiFy\User\Metadata\User as MetadataUser;
use tiFy\User\Metadata\UserOption as MetadataUserOption;
use tiFy\User\Role\Role;
use tiFy\User\Session\Session;
use tiFy\User\SignIn\SignIn;
use tiFy\User\SignUp\SignUp;
use tiFy\User\TakeOver\TakeOver;
use tiFy\User\User;
use tiFy\App\Container\AppServiceProvider;

class UserServiceProvider extends AppServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->singleton(
            User::class,
            function () {
                return new User();
            }
        )->build();

        $this->app->singleton(
            MetadataUser::class
        )->build();

        $this->app->singleton(
            MetadataUserOption::class
        )->build();

        $this->app->singleton(
            Role::class,
            function () {
                return new Role();
            }
        )->build();

        $this->app->singleton(
            Session::class,
            function () {
                return new Session();
            }
        )->build();

        $this->app->singleton(
            SignIn::class,
            function () {
                return new SignIn();
            }
        )->build();

        $this->app->singleton(
            SignUp::class,
            function () {
                return new SignUp();
            }
        )->build();

        $this->app->singleton(
            TakeOver::class,
            function () {
                return new TakeOver();
            }
        )->build();
    }
}