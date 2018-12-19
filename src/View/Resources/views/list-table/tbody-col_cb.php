<?php
/**
 * Colonne "Case à coché" de la ligne de données de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var tiFy\View\Pattern\ListTable\Contracts\Item $item
 * @var tiFy\View\Pattern\ListTable\Contracts\ColumnsItem $column
 */
?>
<th <?php echo $this->get('attrs', ''); ?>>
    <input
        type="checkbox"
        name="<?php echo $item->getPrimaryKey(); ?>[]"
        value="<?php echo $item->getPrimary(); ?>"
    />
</th>