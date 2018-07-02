<?php

namespace tiFy\Components\AdminView\AjaxListTable;

use tiFy\Components\AdminView\ListTable\ListTable;

class AjaxListTable extends ListTable
{
    /**
     * Nombre d'éléments trouvés
     * @var integer
     */
    protected $FoundItems = 0;

    /**
     * Nombre total d'éléments
     * @var integer
     */
    protected $TotalItems = 0;

    /**
     * Nombre total de page d'éléments
     * @var integer
     */
    protected $TotalPages = 0;

    /**
     * {@inheritdoc}
     */
    public function admin_enqueue_scripts()
    {
        parent::admin_enqueue_scripts();

        \wp_enqueue_style(
            'tiFyAdminView-AjaxListTable',
            $this->appAsset('/AdminView/AjaxListTable/css/styles.css'),
            ['datatables'],
            160506
        );
        wp_enqueue_script(
            'tiFyAdminView-AjaxListTable',
            $this->appAsset('/AdminView/AjaxListTable/js/scripts.js'),
            ['datatables'],
            160506,
            true
        );

        // Déclaration des options
        $options = array_diff_key(
            $this->getDatatablesOptions(),
            array_flip(
                [
                    'processing',
                    'serverSide',
                    'deferLoading',
                    'ajax',
                    'drawCallback',
                    'initComplete',
                ]
            )
        );
        wp_localize_script(
            'tiFyAdminView-AjaxListTable',
            'ttiFyAdminViewAjaxListTable',
            [
                'options'       => $options,
                'columns'       => $this->getDatatablesColumns(),
                'language'      => [
                    'url' => $this->getDatatablesLanguageUrl(),
                ],
                'viewName'        => $this->getName(),
                'total_items'   => $this->get_pagination_arg('total_items'),
                'total_pages'   => $this->get_pagination_arg('total_pages'),
                'per_page'      => $this->get_pagination_arg('per_page'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->appAddAction(
            "wp_ajax_{$this->getName()}_get_items",
            [$this, 'wp_ajax_get_items']
        );
        $this->appAddAction(
            "wp_ajax_{$this->getName()}__per_page",
            [$this, 'wp_ajax_get_per_page']
        );
    }

    /**
     * Affichage des lignes
     */
    public function display_rows_or_placeholder()
    {
        if ($this->has_items()) {
            $this->display_rows();
        } else {
            // Remplacement par une valeur vide pour éviter les erreurs dataTables
            echo '';
        }
    }

    /**
     * Données passées dans la requête de récupération Ajax de Datatables.
     *
     * @return array
     */
    public function getDatatablesAjaxData()
    {
        return [];
    }

    /**
     * Définition des propriétés de colonnes de la table.
     *
     * @return array
     */
    public function getDatatablesColumns()
    {
        $columns = [];

        foreach ($this->get_columns() as $name => $title) :
            array_push(
                $columns,
                [
                    'data'      => $name,
                    'name'      => $name,
                    'title'     => $title,
                    'orderable' => false,
                    'visible'   => !in_array($name, $this->HiddenColumns),
                    'className' => "{$name} column-{$name}" . ($this->PrimaryColumn === $name ? ' has-row-actions column-primary' : ''),
                ]
            );
        endforeach;

        return $columns;
    }

    /**
     * Définition du fichier de traduction.
     *
     * @return string
     */
    public function getDatatablesLanguageUrl()
    {
        if (!function_exists('wp_get_available_translations')) :
            require_once(ABSPATH . 'wp-admin/includes/translation-install.php');
        endif;

        $AvailableTranslations = wp_get_available_translations();
        $version = tify_script_get_attr('datatables', 'version');
        $language_url = "//cdn.datatables.net/plug-ins/{$version}/i18n/English.json";

        if (isset($AvailableTranslations[get_locale()])) :
            $file = preg_replace('/\s\(.*\)/', '', $AvailableTranslations[get_locale()]['english_name']);
            if (curl_init("//cdn.datatables.net/plug-ins/{$version}/i18n/{$file}.json")) :
                $language_url = "//cdn.datatables.net/plug-ins/{$version}/i18n/{$file}.json";
            endif;
        endif;

        return $language_url;
    }


    /**
     * Listes des options de Datatables.
     *
     * @return array
     */
    public function getDatatablesOptions()
    {
        return [];
    }

    /**
     * Récupération des données
     */
    protected function getResponse()
    {
        // Récupération des items
        $query = $this->db()->query($this->parse_query_args());
        $items = $query->items;

        $this->TotalItems = $query->found_items;
        $this->PerPage = $this->get_items_per_page($this->db()->Name, $this->PerPage);
        $this->TotalPages = ceil($this->TotalItems / $this->PerPage);

        return $items;
    }

    /**
     * Champs cachés
     */
    public function hidden_fields()
    {
        /**
         * Ajout dynamique d'arguments passés dans la requête ajax de récupération d'éléments
         * ex en PHP : <input type="hidden" id="ajaxDatatablesData" value="<?php echo urlencode( json_encode( array( 'key' => 'value' ) ) );?>"/>
         * ex en JS : $( '#ajaxDatatablesData' ).val( encodeURIComponent( JSON.stringify( resp.data ) ) );
         */
        ?><input type="hidden" id="ajaxDatatablesData" value="<?php echo rawurlencode(json_encode($this->getDatatablesAjaxData())); ?>"/><?php
    }

    /**
     * Traitement des arguments de requête
     */
    public function parse_query_args()
    {
        // Arguments par défaut
        $query_args = [
            'per_page' => $this->PerPage,
        ];

        // Traitement des arguments de requête
        if (isset($_REQUEST['draw'])) :
            $query_args['draw'] = $_REQUEST['draw'];

            if (isset($_REQUEST['length'])) :
                $query_args['per_page'] = $_REQUEST['length'];
            endif;

            if (isset($_REQUEST['length']) && isset($_REQUEST['start'])) :
                $query_args['paged'] = $_REQUEST['paged'] = $query_args['page'] = ceil(($_REQUEST['start'] / $_REQUEST['length']) + 1);
            endif;

            if (isset($_REQUEST['search']) && isset($_REQUEST['search']['value'])) :
                $query_args['search'] = $_REQUEST['search']['value'];
            endif;

            if (isset($_REQUEST['order'])) :
                $query_args['orderby'] = [];
            endif;

            foreach ((array)$_REQUEST['order'] as $k => $v) :
                $query_args['orderby'][$_REQUEST['columns'][$v['column']]['data']] = $v['dir'];
            endforeach;
        endif;

        return $this->QueryArgs = wp_parse_args($this->QueryArgs, $query_args);
    }

    /**
     * Récupération des éléments
     */
    public function prepare_items()
    {
        $res = $this->getResponse();

        // Traitement des erreurs
        if (\is_wp_error($res)) {
            return $res;
        }

        // Définition des éléments
        $this->items = $res;

        // Pagination
        $this->set_pagination_args(
            [
                'total_items' => $this->TotalItems,
                'per_page'    => $this->PerPage,
                'total_pages' => $this->TotalPages,
            ]
        );
    }

    /**
     * Récupération Ajax de la liste des éléments
     */
    public function wp_ajax_get_items()
    {
        // Initialisation des paramètres de configuration de la table
        $this->initParams();

        $res = $this->getResponse();

        // Traitement des erreurs
        if (\is_wp_error($res)) :
            // Pagination
            $this->set_pagination_args(
                [
                    'total_items' => 0,
                    'per_page'    => $this->PerPage,
                    'total_pages' => 0,
                ]
            );
            ob_start();
            $this->pagination('ajax');
            $pagination = ob_get_clean();

            $response = [
                'pagenum'         => 0,
                'draw'            => $_REQUEST['draw'],
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'pagination'      => $pagination,
                'data'            => [],
            ];
        else :
            // Définition des éléments    
            $this->items = $res;

            // Pagination
            $this->set_pagination_args(
                [
                    'total_items' => $this->TotalItems,
                    'per_page'    => $this->PerPage,
                    'total_pages' => $this->TotalPages,
                ]
            );
            ob_start();
            $this->pagination('ajax');
            $pagination = ob_get_clean();

            // Champs de recherche
            ob_start();
            $this->search_box($this->label('search_items'), $this->template()->getID());
            $search_form = ob_get_clean();

            $data = [];
            foreach ((array)$this->items as $i => $item) :
                foreach ((array)$this->get_columns() as $column_name => $column_label) :
                    if ('cb' === $column_name) :
                        $data[$i][$column_name] = $this->column_cb($item);
                    elseif (method_exists($this, '_column_' . $column_name)) :
                        $data[$i][$column_name] = call_user_func(
                            [$this, '_column_' . $column_name],
                            $item,
                            $classes,
                            //$data,
                            $this->PrimaryColumn
                        );
                    elseif (method_exists($this, 'column_' . $column_name)) :
                        $data[$i][$column_name] = call_user_func([$this, 'column_' . $column_name], $item);
                        $data[$i][$column_name] .= $this->handle_row_actions($item, $column_name, $this->PrimaryColumn);
                    else :
                        $data[$i][$column_name] = $this->column_default($item, $column_name);
                        $data[$i][$column_name] .= $this->handle_row_actions($item, $column_name, $this->PrimaryColumn);
                    endif;
                endforeach;
            endforeach;

            $response = [
                'pagenum'         => $this->get_pagenum(),
                'draw'            => $_REQUEST['draw'],
                'recordsTotal'    => $this->_pagination_args['total_items'],
                'recordsFiltered' => $this->_pagination_args['total_items'],
                'pagination'      => $pagination,
                'search_form'     => $search_form,
                'data'            => (array)$data,
            ];
        endif;

        wp_send_json($response);
    }

    /**
     * Récupation Ajax du nombre d'éléments par page
     */
    public function wp_ajax_get_per_page()
    {
        $res = update_user_meta(get_current_user_id(), $this->PerPageName, $_POST['per_page']);
        wp_die();
    }
}