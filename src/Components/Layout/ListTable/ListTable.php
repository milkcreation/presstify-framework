<?php

namespace tiFy\Components\Layout\ListTable;

use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionController;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionController;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterCollectionController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\Layout\ListTable\Item\ItemInterface;
use tiFy\Components\Layout\ListTable\Param\ParamCollectionController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionCollectionController;
use tiFy\Components\Layout\ListTable\TemplateController;
use tiFy\Kernel\Layout\LayoutBaseController;
use tiFy\Kernel\Layout\LayoutControllerInterface;

class ListTable extends LayoutBaseController implements ListTableInterface
{
    use WpListTableTrait;

    /**
     * Controleur du fournisseur de service.
     * @var string
     */
    protected $serviceProvider = ListTableServiceProvider::class;

    /**
     * Liste des classes de rappel des services.
     * @var array
     */
    protected $providers = [
        'columns'   => ColumnCollectionController::class,
        'items'     => ItemCollectionController::class,
        'params'    => ParamCollectionController::class
    ];

    /**
     * Initialisation du controleur.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        $this->appTemplates(
            [
                'directory'  => dirname(__FILE__) . '/Templates',
                'controller' => TemplateController::class,
            ]
        );

        $this->appAddAction(
            "wp_ajax_{$this->getName()}_preview_item",
            [$this, 'wp_ajax_preview_item']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function columns()
    {
        return $this->provide('columns');
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
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        return $this->appTemplateRender('list-table');
        ?>

            <?php if($base_uri_query_vars = $this->getBaseUriQueryVars()) : ?>
                <?php foreach ($base_uri_query_vars as $k => $v) : ?>
                    <input type="hidden" name="<?php echo $k;?>" value="<?php echo $v;?>" />
                <?php endforeach; ?>
            <?php endif; ?>

            <?php $this->hidden_fields(); ?>


            <?php $this->preview_items(); ?>
        <?php
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
        $this->displayTablenavExtra($which);
        $this->pagination($which);
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkActions($which = '')
    {
        echo $this->provide('bulk_actions', [$which]);
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
     * {@inheritdoc}
     */
    public function getRowActions($item, $column_name, $primary)
    {
        if ($primary !== $column_name) :
            return;
        endif;

        return $this->provide('row_actions', [$item]);
    }

    /**
     * {@inheritdoc}
     */
    public function getSingleRow($item)
    {
        list($columns, $hidden, $sortable, $primary) = $this->getColumnInfos();

        $output = '';
        foreach ($columns as $column_name => $column_display_name) :
            $classes = "$column_name column-$column_name";
            $classes .= ($primary === $column_name ) ? ' has-row-actions column-primary' : '';
            $classes .= in_array($column_name, $hidden) ? ' hidden' : '';

            $data = 'data-colname="' . wp_strip_all_tags($column_display_name) . '"';

            $attributes = "class=\"{$classes}\" {$data}";

            if ('cb' === $column_name) :
                $output .= '<th scope="row" class="check-column">';
                $output .= $this->getColumnDisplay($column_name, $item);
                $output .= '</th>';
            else :
                $output .= "<td {$attributes}>";
                $output .= $this->getColumnDisplay($column_name, $item);
                $output .= $this->getRowActions($item, $column_name, $primary);
                $output .= '</td>';
            endif;
        endforeach;

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function getTableClasses()
    {
        return array_merge(
            ['widefat', 'fixed', 'striped', $this->_args['plural']],
            $this->param('table_classes')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFilters()
    {
        return $this->provide('view_filters')->all();
    }

    /**
     * Récupération de la liste des éléments.
     *
     * @return ItemCollectionInterface
     */
    public function items()
    {
        return $this->provide('items');
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
        if (!$items = $this->items()) :
            return;
        endif;

        $total_items = $items->getTotal();
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
        echo $this->display();
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