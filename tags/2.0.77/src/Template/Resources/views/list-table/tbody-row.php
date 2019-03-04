<?php
/**
 * Ligne de données de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer\Viewer $this
 * @var tiFy\Template\Templates\ListTable\Contracts\Item $item
 * @var tiFy\Template\Templates\ListTable\Contracts\ColumnsItem $column
 */
?>
<tr><?php foreach ($this->columns() as $column) : echo $column; endforeach; ?></tr>