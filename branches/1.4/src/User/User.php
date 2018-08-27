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

use tiFy\App\AppController;
use tiFy\User\Role\Role;
use tiFy\User\Session\Session;
use tiFy\User\SignIn\SignIn;
use tiFy\User\SignUp\SignUp;
use tiFy\User\TakeOver\TakeOver;

final class User extends AppController
{
    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function appBoot()
    {
        $this->appServiceShare(Role::class, new Role());
        $this->appServiceShare(Session::class, new Session());
        $this->appServiceShare(SignIn::class, new SignIn());
        $this->appServiceShare(SignUp::class, new SignUp());
        $this->appServiceShare(TakeOver::class, new TakeOver());
    }
}
