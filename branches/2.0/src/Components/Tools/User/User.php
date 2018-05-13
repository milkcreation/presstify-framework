<?php

namespace tiFy\Components\Tools\User;

class User
{
    /**
     * Récupération d'une liste d'utilisateur sous la forme d'un tableau indexé au format clé => valeur.
     * @internal Tous les arguments de requête sont disponibles à l'exception de fields qui est court-circuité par la méthode.
     *
     * @param string $key Champ utilisé comme clé du tableau de sortie.
     * @param string $value Champ utilisé comme valeur du tableau de sortie.
     * @param array $query_args Liste des arguments de requête (hors fields).
     *
     * @return array
     */
    public function userQueryKeyValue($key = 'ID', $value = 'display_name', $query_args = [])
    {
        $users = [];
        $query_args['fields'] = [$key, $value];

        $user_query = new \WP_User_Query($query_args);

        if (empty($user_query->get_results())) :
            return $users;
        endif;

        foreach($user_query->get_results() as $user) :
            $users[$user->{$key}] = $user->{$value};
        endforeach;

        return $users;
    }

    /**
     * Récupération du nom d'affichage d'un rôle.
     *
     * @return string
     */
    public function roleDisplayName($role)
    {
        $wp_roles = new \WP_Roles();
        $roles = $wp_roles->get_names();

        if (!isset($roles[$role])) :
            return $role;
        endif;

        return \translate_user_role($roles[$role]);
    }
}