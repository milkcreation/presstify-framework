<?php
/**
 * Entête de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var tiFy\View\Pattern\ListTable\Columns\ColumnsItem $column
 */
?>
<thead>
    <tr><?php foreach($this->columns() as $column) : echo $column->header(); endforeach; ?></tr>
</thead>
