<?php
/**
 * Ligne de données de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer $this
 * @var tiFy\Template\Templates\ListTable\Contracts\Column $column
 * @var tiFy\Template\Templates\ListTable\Contracts\Item $item
 */
?>
<tr>
    <?php foreach ($this->columns() as $column) : ?>
        <td <?php echo $column->cellAttrs(); ?>>
            <?php echo $column; ?>
        </td>
    <?php endforeach; ?>
</tr>