<?php

namespace tiFy\Components\Layout\ListTable;

use tiFy\Components\Layout\ListTable\BulkAction\BulkActionCollectionController;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionController;
use tiFy\Components\Layout\ListTable\Column\ColumnCollectionInterface;
use tiFy\Components\Layout\ListTable\ViewFilter\ViewFilterCollectionController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionController;
use tiFy\Components\Layout\ListTable\Item\ItemCollectionInterface;
use tiFy\Components\Layout\ListTable\Item\ItemInterface;
use tiFy\Components\Layout\ListTable\RowAction\RowActionCollectionController;
use tiFy\Components\Layout\ListTable\TemplateController;
use tiFy\Kernel\Layout\AbstractLayoutBaseController;
use tiFy\Kernel\Layout\LayoutControllerInterface;

class ListTable extends AbstractLayoutBaseController implements ListTableInterface
{
    /**
     * Controleur du fournisseur de service.
     * @var string
     */
    protected $serviceProvider = ListTableServiceProvider::class;

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
        return $this->provide('columns');
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkActions($which = '')
    {
        echo $this->provide('bulk_actions', [$which]);
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
            ['widefat', 'fixed', 'striped', $this->get('plural')],
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
     * {@inheritdoc}
     */
    public function items()
    {
        return $this->provide('items');
    }

    /**
     * {@inheritdoc}
     */
    public function pagination()
    {
        return $this->provide('pagination');
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        if (!$items = $this->items()) :
            return;
        endif;

        $total_items = $items->getTotal();
        $per_page = $this->request()->getPerPage();

        $this->provide(
            'pagination',
            [
                [
                    'total_items' => $total_items,
                    'per_page'    => $per_page,
                    'total_pages' => ceil($total_items/$per_page)
                ]
            ]
        );
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
        echo $this->appTemplateRender('list-table');
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