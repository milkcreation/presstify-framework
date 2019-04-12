<?php

namespace tiFy\Wordpress\View\Addmin\UserListTable;

use tiFy\Layout\Share\WpUserListTable\WpUserListTable as ShareWpUserListTable;

class UserListTable extends ShareWpUserListTable
{
	/**
	 * Liste des fournisseurs de service.
	 * @var string[]
	 */
	protected $serviceProviders = [
		WpUserListTableServiceProvider::class
	];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        add_action(
            'admin_enqueue_scripts',
            function () {
                wp_enqueue_style(
                    'LayoutAdminWpUserListTable',
                    assets()->url('layout/admin/user-list-table/css/styles.css'),
                    [],
                    171115
                );
            }
        );
    }

	/**
	 * Filtrage de l'argument de requête terme de recherche
	 *
	 * @param string $value Valeur du terme de recherche passé en argument
	 * @param array $query_args Liste des arguments de requête passé par référence
	 *
	 * @return string
	 */
	public function filter_query_arg_s($value, &$query_args)
	{
		if (!empty($value)) :
			$query_args['search'] = '*' . wp_unslash(trim($value)) . '*';
		endif;
	}

	/**
	 * Filtrage de l'argument de requête role
	 *
	 * @param string $value Valeur rôle passé en argument
	 * @param array $query_args Liste des arguments de requête passé par référence
	 *
	 * @return string
	 */
	public function filter_query_arg_role($value, &$query_args)
	{
		if (!empty($value)) :
			if (is_string($value)) :
				$value = array_map('trim', explode(',', $value));
			endif;

			$roles = [];
			foreach ($value as $v) :
				if (!in_array($v, $this->getParam('roles', []))) :
					continue;
				endif;
				array_push($roles, $v);
			endforeach;

			if ($roles) :
				$query_args['role__in'] = $roles;
			endif;
		endif;
	}

	/**
	 * Initialisation de la liste des roles à afficher
	 *
	 * @param array $roles Liste des roles à afficher existants
	 *
	 * @return array
	 */
	public function init_param_roles($roles = [])
	{
		if ($editable_roles = array_reverse(\get_editable_roles())) :
			$editable_roles = array_keys($editable_roles);
		endif;

		$_roles = [];
		if ($roles) :
			foreach ($roles as $role) :
				if (!in_array($role, $editable_roles)) :
					continue;
				endif;
				array_push($_roles, $role);
			endforeach;
		else :
			$_roles = $editable_roles;
		endif;

		return $_roles;
	}
}