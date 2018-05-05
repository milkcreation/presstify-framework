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

use tiFy\Apps\AppController;
use tiFy\User\Login\Login;
use tiFy\User\Role\Role;
use tiFy\User\Session\Session;
use tiFy\User\SignUp\SignUp;
use tiFy\User\TakeOver\TakeOver;

final class User extends AppController
{
    /**
     * CONSTRUCTEUR.
     *
     * @return void
     */
    public function __construct()
    {
        $this->appServiceShare(Login::class, new Login());
        $this->appServiceShare(Role::class, new Role());
        $this->appServiceShare(Session::class, new Session());
        $this->appServiceShare(SignUp::class, new SignUp());
        $this->appServiceShare(TakeOver::class, new TakeOver());
    }
}
