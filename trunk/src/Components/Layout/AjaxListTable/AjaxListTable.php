<?php

namespace tiFy\Components\Layout\AjaxListTable;

use tiFy\Asset\Asset;
use tiFy\Components\Layout\AjaxListTable\AjaxListTableServiceProvider;
use tiFy\Components\Layout\ListTable\ListTable;

class AjaxListTable extends ListTable
{
    /**
     * Controleur du fournisseur de service.
     * @var string
     */
    protected $serviceProvider = AjaxListTableServiceProvider::class;

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        parent::boot();

        $this->appAddAction(
            "wp_ajax_{$this->getName()}_get_items",
            [$this, 'ajaxGetItems']
        );
        $this->appAddAction(
            "wp_ajax_{$this->getName()}__per_page",
            [$this, 'ajaxGetPerPage']
        );

        if (!$this->view()->isAdmin()) :
            $this->appAddAction(
                "wp_ajax_nopriv_{$this->getName()}_get_items",
                [$this, 'ajaxGetItems']
            );
            $this->appAddAction(
                "wp_ajax_nopriv_{$this->getName()}__per_page",
                [$this, 'ajaxGetPerPage']
            );
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        parent::current();

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

        /** @var Asset $assetController */
        $assetController = $this->app->appServiceGet(Asset::class);
        $assetController->setDataJs(
            'dataTables',
            [
                'options'       => $options,
                'columns'       => $this->getDatatablesColumns(),
                /*'language'      => [
                    'url' => $this->getDatatablesLanguageUrl(),
                ],*/
                'viewName'      => $this->getName(),
                'total_items'   => $this->pagination()->getTotalItems(),
                'total_pages'   => $this->pagination()->getTotalPages(),
                'per_page'      => $this->pagination()->getPerPage()
            ],
            $this->view()->getEnv()
        );
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

        foreach ($this->columns()->all() as $name => $c) :
            array_push(
                $columns,
                [
                    'data'      => $c->getName(),
                    'name'      => $c->getName(),
                    'title'     => $c->getHeaderContent(),
                    'orderable' => false,
                    'visible'   => !$c->isHidden(),
                    //'className' => "{$name} column-{$name}" . ($this->PrimaryColumn === $name ? ' has-row-actions column-primary' : ''),
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
     * Récupération Ajax de la liste des éléments
     *
     * @return void
     */
    public function ajaxGetItems()
    {
        $this->app->appRequest('POST')->set('draw', 2);
        $this->prepare();

        list($columns, $hidden, $sortable, $primary) = $this->getColumnInfos();

        $cols = [];
        if ($items = $this->items()->all()) :
            foreach ($items as $i => $item) :
                foreach ($columns as $column_name => $column_display_name) :
                    $classes = "$column_name column-$column_name";
                    $classes .= ($primary === $column_name ) ? ' has-row-actions column-primary' : '';
                    $classes .= in_array($column_name, $hidden) ? ' hidden' : '';

                    $data = 'data-colname="' . wp_strip_all_tags($column_display_name) . '"';

                    $attributes = "class=\"{$classes}\" {$data}";

                    $col = '';
                    if ('cb' === $column_name) :
                        $col .= '<th scope="row" class="check-column">';
                        $col .= $this->getColumnDisplay($column_name, $item);
                        $col .= '</th>';
                    else :
                        $col .= "<td {$attributes}>";
                        $col .= $this->getColumnDisplay($column_name, $item);
                        $col .= $this->getRowActions($item, $column_name, $primary);
                        $col .= '</td>';
                    endif;

                    $cols[$i][$column_name] = $col;
                endforeach;
                $n++;
            endforeach;
        endif;

        $response = [
            'data'            => $cols,
            'draw'            => $this->app->appRequest('POST')->get('draw'),
            'pagenum'         => $this->request()->getPageNum(),
            'pagination'      => $this->appTemplateRender('pagination'),
            'recordsTotal'    => $this->pagination()->getTotalItems(),
            'recordsFiltered' => $this->pagination()->getTotalItems(),
            'search_form'     => $this->appTemplateRender('search-box')
        ];

        wp_send_json($response);
    }

    /**
     * Récupation Ajax du nombre d'éléments par page.
     *
     * @return void
     */
    public function ajaxGetPerPage()
    {
        $res = update_user_meta(get_current_user_id(), $this->PerPageName, $_POST['per_page']);
        wp_die();
    }

    /** OLD */
    /**
     * Récupération des données
     */
    protected function getResponse()
    {
        // Récupération des items
        $query = $this->db()->query($this->parse_query_args());
        $items = $query->getItems();

        $this->TotalItems = $query->getFoundItems();
        $this->PerPage = $this->get_items_per_page($this->db()->Name, $this->PerPage);
        $this->TotalPages = ceil($this->TotalItems / $this->PerPage);

        return $items;
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
}