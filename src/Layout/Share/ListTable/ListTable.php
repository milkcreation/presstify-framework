<?php

namespace tiFy\Layout\Share\ListTable;

use tiFy\Layout\Layout;
use tiFy\Layout\Base\AbstractBaseController;
use tiFy\Layout\Share\ListTable\Contracts\ListTableInterface;

class ListTable extends AbstractBaseController implements ListTableInterface
{
    /**
     * Liste des fournisseurs de service.
     * @var string[]
     */
    protected $serviceProviders = [
        ListTableServiceProvider::class
    ];

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        /** @var Layout $layout */
        $layout = resolve('layout');

        $this->viewer()
            ->setDirectory($layout->resourcesDir('/views/list-table'))
            ->setController(ListTableViewController::class);
    }

    /**
     * {@inheritdoc}
     */
    public function columns()
    {
        return $this->resolve('columns', [$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function getBulkActions($which = '')
    {
        echo $this->resolve('bulk_actions', [$which, $this]);
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

        return $this->resolve('row_actions', [$item, $this]);
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
        return $this->resolve('view_filters', [$this])->all();
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        return $this->resolve('items', [$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function pagination()
    {
        return $this->resolve('pagination', [$this]);
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

        $this->pagination()
            ->set('per_page', $per_page)
            ->set('total_items', $total_items)
            ->set('total_pages', $total_pages);
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
        return $this->viewer('list-table');
    }

    /**
     * Affichage.
     *
     * @return string
     */
    public function display()
    {
        ?>

        <?php if($base_uri_query_vars = $this->getShareUriQueryVars()) : ?>
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