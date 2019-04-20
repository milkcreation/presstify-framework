<?php
/**
 * Pied de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer\Viewer $this
 * @var tiFy\Template\Templates\ListTable\Columns\ColumnsItem $column
 */
?>
<tfoot>
    <tr><?php foreach ($this->columns() as $column) : echo $column->header(false); endforeach; ?></tr>
</tfoot>
