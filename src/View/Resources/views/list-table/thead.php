<?php
/**
 * Entête de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\ListTableViewController $this
 */
?>
<thead>
    <tr>
        <?php echo join('', $this->getHeaderColumns()); ?>
    </tr>
</thead>
