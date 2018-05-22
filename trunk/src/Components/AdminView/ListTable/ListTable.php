<?php

namespace tiFy\Components\AdminView\ListTable;

use tiFy\AdminView\AdminViewBaseController;
use tiFy\AdminView\AdminViewMenuController;
use tiFy\AdminView\Traits\WpListTableTrait;
use tiFy\Components\AdminView\ListTable\ListTableParams;

class ListTable extends AdminViewBaseController
{
    use ParamsTrait, RowActionsTrait, ViewsTrait, WpListTableTrait;

    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        if ($preview_item_mode = $this->params()->get('preview_item_mode')) :
            wp_enqueue_script(
                'tiFyAdminView-ListTable',
                $this->appAsset('/AdminView/ListTable/js/scripts.js'),
                ['jquery', 'url'],
                171118,
                true
            );
            wp_localize_script(
                'tiFyAdminView-ListTable',
                'tiFyAdminViewListTable',
                [
                    'action'          => $this->getName() . '_preview_item',
                    'mode'            => $preview_item_mode,
                    'nonce_action'    => '_wpnonce',
                    'item_index_name' => $this->params()->get('item_index_name'),
                ]
            );

            if ($preview_item_mode === 'dialog') :
                \wp_enqueue_style('wp-jquery-ui-dialog');
                \wp_enqueue_script('jquery-ui-dialog');
            endif;
        endif;
    }

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        $this->init();
        
        $this->menu = new AdminViewMenuController($this->get('admin_menu', []), $this);
        $this->params = new ListTableParams($this->get('params', []), $this);

        $this->appAddAction(
            "wp_ajax_{$this->getName()}_preview_item",
            [$this, 'wp_ajax_preview_item']
        );
    }

    /**
     * Contenu de la colonne - Case à cocher
     * @see \WP_List_Table::column_cb()
     *
     * @param object $item Attributs de l'élément courant
     *
     * @return string
     */
    public function column_cb($item)
    {
        return (($db = $this->getDb()) && ($primary = $db->getPrimary()) && isset($item->{$primary})) ? sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $primary, $item->{$primary}) : parent::column_cb($item);
    }

    /**
     * Contenu par défaut des colonnes
     * @see \WP_List_Table::column_default()
     *
     * @param object $item Attributs de l'élément courant
     * @param string $column_name Identifiant de qualification de la colonne courante
     *
     * @return string
     */
    public function column_default($item, $column_name)
    {
        $custom_columns_content = apply_filters_ref_array("manage_" . $this->getName() . "_custom_column", [null, $column_name, $item]);
        if (!is_null($custom_columns_content)) :
            return $custom_columns_content;
        endif;

        // Bypass
        if (!isset($item->{$column_name})) :
            return;
        endif;

        // Définition du type de données de la valeur de la colonne
        $type = (($db = $this->getDb()) && $db->existsCol($column_name)) ? strtoupper($db->getColAttr($column_name, 'type') ) : '';

        switch($type) :
            default:
                if(is_array($item->{$column_name})) :
                    return join(', ', $item->{$column_name});
                else :
                    return $item->{$column_name};
                endif;
                break;
            case 'DATETIME' :
                return \mysql2date(get_option('date_format') . ' @ ' . get_option('time_format'), $item->{$column_name});
                break;
        endswitch;
    }

    /**
     * Récupération d'élément courant à traiter.
     *
     * @return null|array Identifiant de qualification ou Tableau indexé de la liste des identifiants de qualification
     */
    public function current_item_index()
    {
        if ($item_indexes = $this->getRequestItemIndex()) :
            if (!is_array($item_indexes)) :
                return array_map('trim', explode(',', $item_indexes));
            else :
                return $item_indexes;
            endif;
        endif;
    }

    /**
     * Affichage de l'écran courant.
     *
     * @param \WP_Screen $wp_screen
     *
     * @return void
     */
    public function current_screen($wp_screen)
    {
        // Initialisation de l'émulation de la classe de table native de Wordpress
        $this->_wp_list_table_init(
            [
                'plural'   => $this->params()->get('plural'),
                'singular' => $this->params()->get('singular'),
                'ajax'     => $this->params()->get('ajax'),
                'screen'   => $this->getScreen()
            ]
        );

        // Activation de l'interface de gestion du nombre d'éléments par page
        $this->getScreen()->add_option('per_page', ['option' => $this->params()->get('per_page_option_name')]);

        // Exécution des actions
        $this->process_actions();

        // Préparation de la liste des éléments à afficher
        $this->prepare_items();
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
        if(!empty($value)) :
            $query_args['s'] = wp_unslash( trim( $value ) );
        endif;

        return $value;
    }

    /**
     * Récupération de la liste des actions groupées
     * @see \WP_List_Table::get_bulk_actions()
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        return $this->params()->get('bulk_actions');
    }

    /**
     * Récupération de la liste des colonnes
     * @see \WP_List_Table::get_columns()
     *
     * @return array
     */
    public function get_columns()
    {
        return apply_filters("manage_" . $this->getName() . "_columns", $this->params()->get('columns'));
    }

    /**
     * Récupération de la liste des colonnes de prévisualisation d'un élément
     *
     * @return array
     */
    public function get_preview_item_columns()
    {
        if (!$preview_item_columns = $this->params()->get('preview_item_columns')) :
            $preview_item_columns = $this->get_columns();
            unset($preview_item_columns['cb']);
        endif;

        return $preview_item_columns;
    }

    /**
     * Récupération de la liste des colonnes
     * @see \WP_List_Table::get_sortable_columns()
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        return $this->params()->get('sortable_columns');
    }

    /**
     * Récupération de la liste des classe CSS de la balise table.
     * @see \WP_List_Table::get_sortable_columns()
     *
     * @return array List of CSS classes for the table tag.
     */
    protected function get_table_classes()
    {
        return $this->params()->get('table_classes');
    }

    /**
     * Récupération de la liste des vues filtrées
     * @see \WP_List_Table::get_views()
     *
     * @return array
     */
    public function get_views()
    {
        return $this->parseViews($this->params()->get('views'));
    }

    /**
     * Génération et affichage des actions sur un élément
     * @see \WP_List_Table::handle_row_actions()
     *
     * @param object $item Attributs de l'élément courant
     * @param string $column_name Identifiant de qualification de la colonne courante
     * @param string $primary Identifiant de qualification de la colonne principale
     *
     * @return string
     */
    public function handle_row_actions($item, $column_name, $primary)
    {
        if (!$row_actions = $this->params()->get('row_actions')) :
            return;
        endif;

        if ($primary !== $column_name) :
            return;
        endif;

        return $this->parseRowActions($item, $row_actions);
    }

    /**
     * Récupération de l'entête de colonne.
     *
     * @return void
     */
    public function header_cb()
    {
        return "<input id=\"cb-select-all-1\" type=\"checkbox\" />";
    }

    /**
     * Affichage des champs cachés
     *
     * @return string
     */
    public function hidden_fields()
    {
        if($preview_item_mode = $this->params()->get('preview_item_mode')) :
            ?><input type="hidden" id="PreviewItemAjaxData" value="<?php echo rawurlencode(json_encode($this->params()->get('preview_ajax_datas')));?>" /><?php
        endif;
    }

    /**
     * Initialisation  du titre de la page
     *
     * @param string $page_title Titre de la page défini en paramètre
     *
     * @return string
     */
    public function init_param_page_title($page_title = '')
    {
        if (!$page_title) :
            $page_title = $this->getLabel('all_items', '');
        endif;

        return $page_title;
    }

    /**
     * Récupération du contenu de la table lorsque la liste des éléments est vide
     * @see \WP_List_Table::no_items()
     *
     * @return string
     */
    public function no_items()
    {
        echo $this->params()->get('no_items');
    }

    /**
     * Traitement des arguments de requête
     *
     * @return array Tableau associatif des arguments de requête
     */
    public function parse_query_args()
    {
        if (!$db = $this->getDb()) :
            return;
        endif;

        // Récupération des arguments
        $per_page   = $this->get_items_per_page($this->params()->get('per_page_option_name'), $this->params()->get('per_page'));
        $paged      = $this->get_pagenum();

        // Arguments par défaut
        $query_args = [
            'per_page' => $per_page,
            'paged'    => $paged,
            'order'    => 'DESC',
            'orderby'  => $db->getPrimary()
        ];
        $query_args = \wp_parse_args($this->params()->get('query_args', []), $query_args);

        // Traitement des arguments de requête
        if ($request_query_vars = $this->getRequestQueryVars()) :
            foreach($request_query_vars as $key => $value) :
                if (method_exists($this, "filter_query_arg_{$key}")) :
                    $query_args[$key] = call_user_func_array([$this, "filter_query_arg_{$key}"], [$value, &$query_args]);
                elseif($db->existsCol($key)) :
                    $query_args[$key] = $value;
                endif;
            endforeach;
        endif;

        return $query_args;
    }

    /**
     * Préparation de la liste des éléments à afficher.
     *
     * @return void
     */
    public function prepare_items()
    {
        if (!$db = $this->getDb()) :
            return;
        endif;

        $query_args = $this->parse_query_args();
        $query = $db->query($query_args);

        $this->items = $query->getItems();

        $total_items = $query->getFoundItems();
        $per_page = $this->get_items_per_page($this->params()->get('per_page_option_name'), $this->params()->get('per_page'));

        $this->set_pagination_args(
            [
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items/$per_page)
            ]
        );
    }

    /**
     * Affichage de l'aperçu des données d'un élément
     *
     * @param object $item Attributs de l'élément courant
     *
     * @return string
     */
    public function preview_item($item)
    {
        if (!$preview_item_columns = $this->get_preview_item_columns()) :
            return;
        endif;
        ?>
        <table class="form-table">
            <tbody>
            <?php foreach ($preview_item_columns as $column_name => $column_label) :?>
                <tr valign="top">
                    <th scope="row">
                        <label><strong><?php echo $column_label;?></strong></label>
                    </th>
                    <td>
                        <?php echo $this->preview_item_default($item, $column_name); ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
        <div class="clear"></div>
        <?php
    }

    /**
     * Affichage de l'aperçu des données d'un élément par défaut
     *
     * @param object $item Attributs de l'élément courant
     * @param string $column_name Identifiant de qualification de la colonne
     *
     * @return string
     */
    public function preview_item_default($item, $column_name)
    {
        if (method_exists($this, "preview_item_{$column_name}")) :
            return call_user_func([$this, "preview_item_{$column_name}"], $item);
        elseif (method_exists($this, '_column_' . $column_name)) :
            return call_user_func([$this, '_column_' . $column_name], $item);
        elseif (method_exists($this, 'column_' . $column_name)) :
            return call_user_func([$this, 'column_' . $column_name], $item);
        else :
            return $this->column_default($item, $column_name);
        endif;
    }

    /**
     * Aperçu des données des éléments
     *
     * @return string
     */
    public function preview_items()
    {
        switch($this->params()->get('preview_item_mode')) :
            case 'dialog' :
                ?><div id="Item-previewContainer" class="hidden" style="max-width:800px; min-width:800px;"><div class="Item-previewContent"></div></div><?php
                break;
            case 'row' :
                ?><table class="hidden"><tbody><tr id="Item-previewContainer"><td class="Item-previewContent" colspan="<?php echo count($this->get_columns());?>"><h3><?php _e( 'Chargement en cours ...', 'tify' );?></h3></td></tr></tbody></table><?php
                break;
        endswitch;
    }

    /**
     * Affichage de la page.
     *
     * @return string
     */
    public function render()
    {
        ?>
        <div class="wrap">
            <h2>
                <?php echo $this->params()->get('page_title');?>

                <?php if($edit_base_uri = $this->params()->get('edit_base_uri')) : ?>
                    <a class="add-new-h2" href="<?php echo $edit_base_uri;?>"><?php echo $this->getLabel('add_new');?></a>
                <?php endif;?>
            </h2>

            <?php $this->views(); ?>

            <form method="get" action="">
                <?php if($base_uri_query_vars = $this->getBaseUriQueryVars()) : ?>
                    <?php foreach ($base_uri_query_vars as $k => $v) : ?>
                        <input type="hidden" name="<?php echo $k;?>" value="<?php echo $v;?>" />
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php $this->hidden_fields();?>

                <?php $this->search_box($this->getLabel('search_items'), $this->getName());?>

                <?php $this->display();?>

                <?php $this->preview_items();?>
            </form>
        </div>
        <?php
    }

    /**
     * Récupération ajax de la prévisualisation d'un élément
     *
     * @return string
     */
    public function wp_ajax_preview_item()
    {
        $this->initParams();

        if (!$item_index = $this->getRequestItemIndex()) :
            die(0);
        endif;

        check_ajax_referer($this->getActionNonce('preview_item', $item_index));

        $this->prepare_items();
        $item = current($this->items);
        $this->preview_item($item);
        die();
    }
}