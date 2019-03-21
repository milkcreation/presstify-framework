<?php

namespace tiFy\Wp\User;

use Illuminate\Support\Collection;
use tiFy\Contracts\User\RoleFactory;
use tiFy\Contracts\User\UserManager;
use tiFy\Contracts\Wp\User as UserContract;
use tiFy\Wp\User\Signin\SigninFactory;
use WP_User_Query;
use WP_Roles;

class User implements UserContract
{
    /**
     * Instance du gestionnaire utilisateur.
     * @var UserManager
     */
    protected $manager;

    /**
     * CONSTRUCTEUR
     *
     * @param UserManager $manager Instance du gestionnaire utilisateur.
     *
     * @return void
     */
    public function __construct(UserManager $manager)
    {
        $this->manager = $manager;

        add_action('init', function () {
            foreach (config('user.role', []) as $name => $attrs) {
                $this->manager->role()->register($name, $attrs);
            }
        }, 0);

        add_action('init', function() {
            foreach (config('user.signin', []) as $name => $attrs) {
                $this->manager->signin()->register($name, $attrs);
            }
            foreach (config('user.signup', []) as $name => $attrs) {
                $this->manager->signup()->register($name, $attrs);
            }
        }, 999998);

        add_action('profile_update', function($user_id) {
            $this->manager->meta()->Save($user_id);
            $this->manager->option()->Save($user_id);
        }, 2);

        add_action('user_register', function($user_id) {
            $this->manager->meta()->Save($user_id);
            $this->manager->option()->Save($user_id);
        });

        events()->on('user.role.factory.boot', function (RoleFactory $factory){
            /** @var \WP_Roles $wp_roles */
            global $wp_roles;

            $name = $factory->getName();

            /** @var \WP_Role $role */
            if (!$role = $wp_roles->get_role($name)) :
                $role = $wp_roles->add_role($name, $factory->get('display_name'));
            elseif (($names = $wp_roles->get_names()) && ($names[$name] !== $factory->get('display_name'))) :
                $wp_roles->remove_role($name);
                $role = $wp_roles->add_role($name, $factory->get('display_name'));
            endif;

            foreach ($factory->get('capabilities', []) as $cap => $grant) :
                if (!isset($role->capabilities[$cap]) || ($role->capabilities[$cap] !== $grant)) :
                    $role->add_cap($cap, $grant);
                endif;
            endforeach;
        });

        $this->register();
    }

    /**
     * Déclaration des surchages de service du conteneur d'injection.
     *
     * @return void
     */
    public function register()
    {
        app()->add('user.signin.factory', function ($name, $attrs) {
            return new SigninFactory($name, $attrs);
        });
    }

    /**
     * @inheritdoc
     */
    public function pluck($value = 'display_name', $key = 'ID', $query_args = [])
    {
        $users = [];
        $query_args['fields'] = [$key, $value];

        $user_query = new WP_User_Query($query_args);

        if (empty($user_query->get_results())) :
            return $users;
        endif;

        return (new Collection($user_query->get_results()))->pluck($value, $key)->all();
    }

    /**
     * @inheritdoc
     */
    public function roleDisplayName($role)
    {
        $wp_roles = new WP_Roles();
        $roles = $wp_roles->get_names();

        if (!isset($roles[$role])) :
            return $role;
        endif;

        return translate_user_role($roles[$role]);
    }
}