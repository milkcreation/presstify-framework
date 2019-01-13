<?php
/**
 * Ligne de données de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var tiFy\View\Pattern\ListTable\Contracts\Item $item
 * @var tiFy\View\Pattern\ListTable\Contracts\ColumnsItem $column
 */
?>
<tr><?php foreach ($this->columns() as $column) : echo $column; endforeach; ?></tr>