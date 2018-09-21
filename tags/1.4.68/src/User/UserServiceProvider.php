<?php

/**
 * @name User
 * @desc Gestion des utilisateurs
 * @package presstiFy
 * @namespace tiFy\User
 * @version 1.1
 * @subpackage Core
 * @since 1.2.535
 *
 * @author Jordy Manner <jordy@tigreblanc.fr>
 * @copyright Milkcreation
 */

namespace tiFy\User;

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
