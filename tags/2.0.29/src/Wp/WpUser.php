<?php

namespace tiFy\Wp;

use Illuminate\Support\Collection;
use WP_User_Query;
use WP_Roles;

class WpUser
{
    /**
     * Récupération d'une liste d'utilisateur sous la forme d'un tableau indexé au format clé => valeur.
     * @internal Tous les arguments de requête sont disponibles à l'exception de fields qui est court-circuité par la méthode.
     *
     * @param string $value Champ utilisé comme valeur du tableau de sortie.
     * @param string $key Champ utilisé comme clé du tableau de sortie.
     * @param array $query_args Liste des arguments de requête (hors fields).
     *
     * @return array
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
     * Récupération du nom d'affichage d'un rôle.
     *
     * @return string
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