<?php
/**
 * Entête de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Layout\Share\ListTable\ListTableViewController $this.
 */
?>

<thead>
    <tr>
        <?php echo join('', $this->getHeaderColumns()); ?>
    </tr>
</thead>
