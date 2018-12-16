<?php
/**
 * Colonne par défaut d'une ligne de données de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var tiFy\View\Pattern\ListTable\Contracts\Item $item
 * @var tiFy\View\Pattern\ListTable\Contracts\ColumnsItem $column
 */
?>
<td <?php echo $this->get('attrs', ''); ?>>
    <?php echo $this->get('content', ''); ?>
</td>