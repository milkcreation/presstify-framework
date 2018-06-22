<?php

namespace tiFy\Components\AdminView\ListTable;

use tiFy\AdminView\AdminViewBaseController;
use tiFy\Components\AdminView\ListTable\BulkAction\BulkActionCollectionController;
use tiFy\Components\AdminView\ListTable\Column\ColumnCollectionController;
use tiFy\Components\AdminView\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\AdminView\ListTable\Filter\FilterCollectionController;
use tiFy\Components\AdminView\ListTable\Item\ItemCollectionController;
use tiFy\Components\AdminView\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\AdminView\ListTable\Item\ItemInterface;
use tiFy\Components\AdminView\ListTable\Param\ParamCollectionController;
use tiFy\Components\AdminView\ListTable\RowAction\RowActionCollectionController;

class ListTable extends AdminViewBaseController
{
    use WpListTableTrait;

    /**
     * Liste des classes de rappel des services.
     * @var array
     */
    protected $providers = [
        'params'    => ParamCollectionController::class,
        'columns'   => ColumnCollectionController::class,
        'items'     => ItemCollectionController::class
    ];

    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function admin_enqueue_scripts()
    {
        if ($preview_item_mode = $this->param('preview_item_mode')) :
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
                    'item_index_name' => $this->param('item_index_name'),
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
        parent::boot();

        if ($columns = $this->getConcrete('columns')) :
            $this->appServiceAdd(
                ColumnCollectionInterface::class,
                new $columns($this->param('columns', []), $this)
            );
        endif;

        $this->appAddAction(
            "wp_ajax_{$this->getName()}_preview_item",
            [$this, 'wp_ajax_preview_item']
        );
    }

    /**
     * Récupération du controleur de gestion des colonnes.
     *
     * @return ColumnCollectionInterface
     */
    public function columns()
    {
        return $this->appServiceGet(ColumnCollectionInterface::class);
    }

    /**
     * Contenu de la colonne - Case à cocher
     *
     * @param ItemInterface $item Attributs de l'élément courant.
     *
     * @return string
     */
    public function column_cb($item)
    {
        return (($db = $this->getDb()) && ($primary = $db->getPrimary()) && isset($item->{$primary})) ? sprintf('<input type="checkbox" name="%1$s[]" value="%2$s" />', $primary, $item->{$primary}) : parent::column_cb($item);
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
                'plural'   => $this->param('plural'),
                'singular' => $this->param('singular'),
                'ajax'     => $this->param('ajax'),
                'screen'   => $this->getScreen()
            ]
        );

        // Activation de l'interface de gestion du nombre d'éléments par page
        $this->getScreen()->add_option('per_page', ['option' => $this->param('per_page_option_name')]);

        // Exécution des actions
        $this->process_actions();

        // Préparation de la liste des éléments à afficher
        $this->prepare_items();
    }

    /**
     * Affichage du selecteur d'action groupées.
     *
     * @param string $which Choix de l'interface de navigation. top|bottom.
     *
     * @return void
     */
    protected function displayBulkActions($which = '')
    {
        echo new BulkActionCollectionController($this->param('bulk_actions', []), $which, $this);
    }

    /**
     * Affichage du corps de la table.
     *
     * @return void
     */
    public function displayBody()
    {
        /*if ($this->items()->has()) :
            $this->displayRows();
        else : */
            echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
            $this->displayNoItems();
            echo '</td></tr>';
        //endif;
    }

    /**
     * Affichage de la liste des filtres.
     *
     * @return void
     */
    protected function displayFilters()
    {
        if (!$filters = $this->getFilters()) :
            return;
        endif;

        $this->getScreen()->render_screen_reader_content('heading_views');

        echo "<ul class='subsubsub'>\n";
        foreach ($filters as $class => $filter) :
            $filters[$class] = "\t<li class='$class'>$filter";
        endforeach;
        echo implode( " |</li>\n", $filters ) . "</li>\n";
        echo "</ul>";
    }

    /**
     * Affichage du message indiquant que la liste des éléments est vide.
     *
     * @return void
     */
    public function displayNoItems()
    {
        echo $this->param('no_items', __('No items found.'));
    }

    /**
     * Affichage de la liste des lignes de la table.
     *
     * @return void
     */
    public function displayRows()
    {
        foreach ($this->items() as $item) :
            $this->displaySingleRow($item);
        endforeach;
    }

    /**
     * Affichage d'une ligne de la table.
     *
     * @param ItemInterface $item Liste des données de l'élément courant.
     *
     * @return void
     */
    public function displaySingleRow($item)
    {
        echo '<tr>';
        $this->displaySingleRowColumns($item);
        echo '</tr>';
    }

    /**
     * Affichage de la liste des colonnes d'un ligne de la table.
     *
     * @param ItemInterface $item Liste des données de l'élément courant.
     *
     * @return void
     */
    protected function displaySingleRowColumns($item)
    {
        list($columns, $hidden, $sortable, $primary) = $this->getColumnInfos();

        foreach ($columns as $column_name => $column_display_name) :
            $classes = "$column_name column-$column_name";
            $classes .= ($primary === $column_name ) ? ' has-row-actions column-primary' : '';
            $classes .= in_array($column_name, $hidden) ? ' hidden' : '';

            $data = 'data-colname="' . wp_strip_all_tags($column_display_name) . '"';

            $attributes = "class=\"{$classes}\" {$data}";

            if ('cb' === $column_name) :
                echo '<th scope="row" class="check-column">';
                echo $this->column_cb( $item );
                echo '</th>';
            else :
                echo "<td $attributes>";
                echo $this->getColumnDisplay($column_name, $item);
                echo $this->getRowActions($item, $column_name, $primary);
                echo "</td>";
            endif;
        endforeach;
    }

    /**
     * Affichage de la table.
     *
     * @return void
     */
    protected function displayTable()
    {
        $singular = $this->_args['singular'];

        $this->displayTablenav('top');

        $this->getScreen()->render_screen_reader_content('heading_list');
        ?>
        <table class="wp-list-table <?php echo implode(' ', $this->getTableClasses()); ?>">
            <thead>
                <tr>
                    <?php $this->print_column_headers(); ?>
                </tr>
            </thead>

            <tbody id="the-list"<?php echo $singular ? " data-wp-lists=\"list:{$singular}\"" : ''; ?>>
                <?php $this->displayBody(); ?>
            </tbody>

            <tfoot>
                <tr>
                    <?php $this->print_column_headers( false ); ?>
                </tr>
            </tfoot>

        </table>
        <?php
        $this->displayTablenav('bottom');
    }

    /**
     * Affichage des l'interface de navigation de la table.
     *
     * @param string $which Choix de l'interface de navigation. top|bottom.
     *
     * @return void
     */
    protected function displayTablenav($which)
    {
        if ('top' === $which) :
            wp_nonce_field( 'bulk-' . $this->_args['plural'] );
        endif;
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <?php if ($this->has_items()): ?>
                <div class="alignleft actions bulkactions">
                    <?php $this->displayBulkActions($which); ?>
                </div>
            <?php endif;
            $this->displayTablenavExtra($which);
            $this->pagination( $which );
            ?>

            <br class="clear" />
        </div>
        <?php
    }

    /**
     * Affichage des l'interface de navigation complémentaire de la table.
     *
     * @param string $which Choix de l'interface de navigation. top|bottom.
     *
     * @return void
     */
    protected function displayTablenavExtra($which)
    {

    }

    /**
     * Return number of visible columns
     *
     * @since 3.1.0
     *
     * @return int
     */
    public function get_column_count() {
        list ( $columns, $hidden ) = $this->getColumnInfos();
        $hidden = array_intersect( array_keys( $columns ), array_filter( $hidden ) );
        return count( $columns ) - count( $hidden );
    }

    /**
     * Récupération de l'affichage d'une colonne.
     *
     * @param string $name Nom de qualification de la colonne.
     * @param ItemInterface $item Données de l'élément courant à afficher.
     *
     * @return string
     */
    protected function getColumnDisplay($name, $item)
    {
        return $this->columns()->get($name)->display($item);
    }

    /**
     * Récupération des information complètes concernant les colonnes
     *
     * @return array
     */
    protected function getColumnInfos()
    {
        return $this->columns()->getInfos();
    }

    /**
     * Récupération de la liste des colonnes.
     *
     * @return array
     */
    protected function getColumns()
    {
        return $this->columns()->getList();
    }

    /**
     * Récupération de la liste des filtres.
     *
     * @return array
     */
    protected function getFilters()
    {
        return (new FilterCollectionController($this->param('views'), $this))->all();
    }

    /**
     * Récupération de la liste des colonnes masquées.
     *
     * @return array
     */
    protected function getHiddenColumns()
    {
        return $this->columns()->getHidden();
    }


    /**
     * Récupération de la liste des actions sur un élément.
     *
     * @param ItemInterface $item Liste des données de l'élément courant.
     * @param string $column_name Nom de qualification de la colonne courante.
     * @param string $primary Identifiant de qualification de la colonne principale
     *
     * @return string
     */
    public function getRowActions($item, $column_name, $primary)
    {
        if (!$row_actions = $this->param('row_actions')) :
            return;
        endif;

        if ($primary !== $column_name) :
            return;
        endif;

        return new RowActionCollectionController($row_actions, $item, $this);
    }

    /**
     * Récupération de la liste des colonnes pouvant être ordonnancées.
     *
     * @return array
     */
    protected function getSortableColumns()
    {
        return $this->columns()->getSortable();
    }

    /**
     * Récupération de la liste des éléments.
     *
     * @return ItemCollectionInterface
     */
    public function items()
    {
        return $this->appServiceGet(ItemCollectionInterface::class);
    }

    /**
     * Récupération de la liste des arguments de requête
     *
     * @return array
     */
    public function getQueryArgs()
    {
        $query_args = $this->param('query_args', []);

        if (!$db = $this->getDb()) :
            return $query_args;
        endif;

        $per_page = $this->get_items_per_page($this->param('per_page_option_name'), $this->param('per_page'));
        $paged = $this->get_pagenum();

        $query_args = array_merge(
            [
                'per_page' => $per_page,
                'paged'    => $paged,
                'order'    => 'DESC',
                'orderby'  => $db->getPrimary()
            ],
            $query_args
        );

        /*
        if ($request_query_vars = $this->getRequestQueryVars()) :
            foreach($request_query_vars as $key => $value) :
                if (method_exists($this, "filter_query_arg_{$key}")) :
                    $query_args[$key] = call_user_func_array([$this, "filter_query_arg_{$key}"], [$value, &$query_args]);
                elseif($db->existsCol($key)) :
                    $query_args[$key] = $value;
                endif;
            endforeach;
        endif;
        */

        return $query_args;
    }

    /**
     * Récupération de la liste des classe CSS de la balise table.
     *
     * @return array
     */
    protected function getTableClasses()
    {
        return array_merge(
            ['widefat', 'fixed', 'striped', $this->_args['plural']],
            $this->param('table_classes')
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
        if(!empty($value)) :
            $query_args['s'] = wp_unslash( trim( $value ) );
        endif;

        return $value;
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
        if($preview_item_mode = $this->param('preview_item_mode')) :
            ?><input type="hidden" id="PreviewItemAjaxData" value="<?php echo rawurlencode(json_encode($this->param('preview_ajax_datas')));?>" /><?php
        endif;
    }

    /**
     * Préparation de la liste des éléments à afficher.
     *
     * @return void
     */
    public function prepare_items()
    {
        if (!$items = $this->getConcrete('items')) :
            return;
        endif;

        $this->appServiceAdd(ItemCollectionInterface::class, new $items($this->getQueryArgs(), $this));

        $total_items = $this->items()->getTotal();
        $per_page = $this->get_items_per_page($this->param('per_page_option_name'), $this->param('per_page'));

        $this->set_pagination_args(
            [
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items/$per_page)
            ]
        );
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
                <?php echo $this->param('page_title', $this->getLabel('all_items', '')); ?>

                <?php if($edit_base_uri = $this->param('edit_base_uri')) : ?>
                    <a class="add-new-h2" href="<?php echo $edit_base_uri;?>"><?php echo $this->getLabel('add_new');?></a>
                <?php endif;?>
            </h2>

            <?php $this->displayFilters(); ?>

            <form method="get" action="">
                <?php if($base_uri_query_vars = $this->getBaseUriQueryVars()) : ?>
                    <?php foreach ($base_uri_query_vars as $k => $v) : ?>
                        <input type="hidden" name="<?php echo $k;?>" value="<?php echo $v;?>" />
                    <?php endforeach; ?>
                <?php endif; ?>

                <?php $this->hidden_fields(); ?>

                <?php $this->search_box($this->getLabel('search_items'), $this->getName()); ?>

                <?php $this->displayTable(); ?>

                <?php $this->preview_items(); ?>
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


    /**
     * Récupération de la liste des colonnes de prévisualisation d'un élément
     *
     * @return array
     */
    public function get_preview_item_columns()
    {
        if (!$preview_item_columns = $this->param('preview_item_columns')) :
            $preview_item_columns = $this->getColumns();
            unset($preview_item_columns['cb']);
        endif;

        return $preview_item_columns;
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
            return $this->getColumnDisplay($column_name, $item);
        endif;
    }

    /**
     * Aperçu des données des éléments
     *
     * @return string
     */
    public function preview_items()
    {
        switch($this->param('preview_item_mode')) :
            case 'dialog' :
                ?><div id="Item-previewContainer" class="hidden" style="max-width:800px; min-width:800px;"><div class="Item-previewContent"></div></div><?php
                break;
            case 'row' :
                ?><table class="hidden"><tbody><tr id="Item-previewContainer"><td class="Item-previewContent" colspan="<?php echo count($this->getColumns());?>"><h3><?php _e( 'Chargement en cours ...', 'tify' );?></h3></td></tr></tbody></table><?php
                break;
        endswitch;
    }
}