<?php
/**
 * Colonne par défaut d'une ligne de données de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer\Viewer $this
 * @var tiFy\Template\Templates\ListTable\Contracts\Item $item
 * @var tiFy\Template\Templates\ListTable\Contracts\ColumnsItem $column
 */
?>
<td <?php echo $this->get('attrs', ''); ?>>
    <?php echo $this->get('content', ''); ?>
</td>