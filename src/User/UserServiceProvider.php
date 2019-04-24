<?php

namespace tiFy\User;

use tiFy\Container\ServiceProvider;
use tiFy\User\Metadata\Metadata;
use tiFy\User\Metadata\Option as MetaOption;
use tiFy\User\Role\RoleManager;
use tiFy\User\Session\SessionManager;
use tiFy\User\Session\SessionStore;
use tiFy\User\Signin\SigninFactory;
use tiFy\User\Signin\SigninManager;
use tiFy\User\Signup\SignupManager;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Liste des noms de qualification des services fournis.
     * @internal requis. Tous les noms de qualification de services à traiter doivent être renseignés.
     * @var string[]
     */
    protected $provides = [
        'user',
        'user.meta',
        'user.option',
        'user.role',
        'user.session',
        'user.session.store',
        'user.signin',
        'user.signin.factory',
        'user.signup'
    ];

    /**
     * @inheritdoc
     */
    public function boot()
    {

    }

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->getContainer()->share('user', function () {
            return new UserManager();
        });

        $this->getContainer()->share('user.meta', function () {
            return new Metadata();
        });

        $this->getContainer()->share('user.option', function () {
            return new MetaOption();
        });

        $this->getContainer()->share('user.role', function () {
            return new RoleManager();
        });

        $this->getContainer()->share('user.session', function () {
            return new SessionManager();
        });

        $this->getContainer()->add('user.session.store', function ($name, $attrs = []) {
            return new SessionStore($name, $attrs);
        });

        $this->getContainer()->share('user.signin', function () {
            return new SigninManager();
        });

        $this->getContainer()->add('user.signin.factory', function ($name, $attrs = []) {
            return new SigninFactory($name, $attrs);
        });

        $this->getContainer()->share('user.signup', function () {
            return new SignupManager();
        });
    }
}