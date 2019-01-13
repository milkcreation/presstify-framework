<?php

namespace tiFy\Wp\User;

use Illuminate\Support\Collection;
use tiFy\Contracts\Wp\User as UserContract;
use WP_User_Query;
use WP_Roles;

class User implements UserContract
{
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