<?php

namespace tiFy\Components\Layout\ListTable;

use tiFy\App\Layout\AbstractLayoutBaseController;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionInterface;
use tiFy\Components\Layout\ListTable\BulkAction\BulkActionItemTrashController;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\Layout\ListTable\Column\ColumnItemCbController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\Layout\ListTable\Item\ItemInterface;
use tiFy\Components\Layout\ListTable\Pagination\PaginationInterface;
use tiFy\Components\Layout\ListTable\ListTableServiceProvider;
use tiFy\Components\Layout\ListTable\TemplateController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionCollectionInterface;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemActivateController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemDeactivateController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemDeleteController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemDuplicateController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemEditController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemPreviewController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemTrashController;
use tiFy\Components\Layout\ListTable\RowAction\RowActionItemUntrashController;
use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterCollectionInterface;

class ListTable extends AbstractLayoutBaseController implements ListTableInterface
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $providers = [
        ListTableServiceProvider::class
    ];

    /**
     * {@inheritdoc}
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
    }

    /**
     * {@inheritdoc}
     */
    public function columns()
    {
        return $this->resolve(ColumnCollectionInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return array_merge(
            parent::getAliases(),
            [
                'columns.item.cb'             => ColumnItemCbController::class,
                'bulk_actions.item.trash'     => BulkActionItemTrashController::class,
                'row_actions.item.activate'   => RowActionItemActivateController::class,
                'row_actions.item.deactivate' => RowActionItemDeactivateController::class,
                'row_actions.item.delete'     => RowActionItemDeleteController::class,
                'row_actions.item.duplicate'  => RowActionItemDuplicateController::class,
                'row_actions.item.edit'       => RowActionItemEditController::class,
                'row_actions.item.preview'    => RowActionItemPreviewController::class,
                'row_actions.item.trash'      => RowActionItemTrashController::class,
                'row_actions.item.untrash'    => RowActionItemUntrashController::class,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkActions($which = '')
    {
        echo $this->resolve(BulkActionCollectionInterface::class, [$which]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnDisplay($name, $item)
    {
        return $this->columns()->get($name)->display($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnInfos()
    {
        return $this->columns()->getInfos();
    }

    /**
     * {@inheritdoc}
     */
    public function getRowActions($item, $column_name, $primary)
    {
        if ($primary !== $column_name) :
            return;
        endif;

        return $this->resolve(RowActionCollectionInterface::class, [$item]);
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
        return sprintf(
            $this->param('table_classes', '%s'),
            'widefat fixed striped '. $this->get('plural')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFilters()
    {
        return $this->resolve(ViewFilterCollectionInterface::class)->all();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        return $this->resolve(ItemCollectionInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function pagination()
    {
        return $this->resolve(PaginationInterface::class, [[]]);
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        if (!$this->items()->all()) :
            return;
        endif;

        $total_items = $this->items()->getTotal();
        $per_page = $this->request()->getPerPage();
        $total_pages = ceil($total_items/$per_page);

        $this->resolve(PaginationInterface::class, [compact('per_page', 'total_items', 'total_pages')]);
    }

    /**
     * {@inheritdoc}
     */
    public function process()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return $this->appTemplateRender('list-table');
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
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
}