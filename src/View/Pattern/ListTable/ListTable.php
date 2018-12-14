<?php

namespace tiFy\View\Pattern\ListTable;

use tiFy\View\Pattern\ListTable\Contracts\Item;
use tiFy\View\Pattern\ListTable\Contracts\ListTable as ListTableContract;
use tiFy\View\Pattern\ListTable\Contracts\Request;
use tiFy\View\Pattern\PatternController;

class ListTable extends PatternController implements ListTableContract
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
    public function bulkActions()
    {
        return $this->get('bulk-actions');
    }

    /**
     * {@inheritdoc}
     */
    public function columns()
    {
        return $this->get('columns');
    }

    /**
     * Mise en file des scripts de l'interface d'administration.
     *
     * @return void
     */
    public function enqueueScripts()
    {
        if ($preview_item_mode = $this->param('preview_item_mode')) :
            wp_enqueue_script(
                'ViewPattern-listTable',
                '', //$this->appAssetUrl('/AdminView/ListTable/js/scripts.js'),
                ['jquery', 'url'],
                171118,
                true
            );

            wp_localize_script(
                'ViewPattern-listTable',
                'ViewPattern-listTable',
                [
                    'action'          => $this->name() . '_preview_item',
                    'mode'            => $preview_item_mode,
                    'nonce_action'    => '_wpnonce',
                    'item_index_name' => $this->param('item_index_name'),
                ]
            );

            if ($preview_item_mode === 'dialog') :
                wp_enqueue_style('wp-jquery-ui-dialog');
                wp_enqueue_script('jquery-ui-dialog');
            endif;
        endif;
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnDisplay($name, Item $item)
    {
        return $this->columns()->get($name)->display($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getTableClasses()
    {
        return sprintf(
            $this->param('table_classes', '%s'),
            'widefat fixed striped '. $this->param('plural')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function items()
    {
        return $this->get('items');
    }

    /**
     * {@inheritdoc}
     */
    public function pagination()
    {
        return $this->get('pagination');
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        $this->items()->query();

        if (!$this->items()->exists()) :
            return;
        endif;

        $total_items = $this->items()->getFounds();
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
     * {@inheritdoc}
     *
     * @return Request
     */
    public function request()
    {
        return parent::request();
    }

    /**
     * {@inheritdoc}
     */
    public function row(Item $item)
    {
        list($columns, $hidden, $sortable, $primary) = $this->columns()->getInfos();

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
                $output .= (string) $this->rowActions($item, $column_name, $primary);
                $output .= '</td>';
            endif;
        endforeach;

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function rowActions(Item $item, $column_name, $primary)
    {
        if ($primary !== $column_name) :
            return '';
        endif;

        $this->extend('row-actions')->withArguments([$this->param('row_actions', []), $item, $this]);

        return $this->get('row-actions');
    }

    /**
     * {@inheritdoc}
     */
    public function viewFilters()
    {
        return $this->get('view-filters');
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