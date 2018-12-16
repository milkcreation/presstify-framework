<?php
/**
 * Pied de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var tiFy\View\Pattern\ListTable\Columns\ColumnsItem $column
 */
?>
<tfoot>
    <tr><?php foreach ($this->columns() as $column) : echo $column->header(false); endforeach; ?></tr>
</tfoot>
