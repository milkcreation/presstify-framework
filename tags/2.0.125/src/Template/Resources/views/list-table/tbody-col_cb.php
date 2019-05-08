<?php
/**
 * Colonne "Case à coché" de la ligne de données de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer\Viewer $this
 * @var tiFy\Template\Templates\ListTable\Contracts\ColumnsItem $column
 * @var tiFy\Template\Templates\ListTable\Contracts\Item|WP_Post $item
 * @var string $content
 */
?>
<input
    type="checkbox"
    name="<?php echo $item->getPrimaryKey(); ?>[]"
    value="<?php echo $item->getPrimary(); ?>"
/>