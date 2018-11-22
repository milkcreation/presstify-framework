<?php
/**
 * Pied de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Layout\Share\ListTable\ListTableViewController $this.
 */
?>

<tfoot>
    <tr>
        <?php echo join('', $this->getHeaderColumns(false)); ?>
    </tr>
</tfoot>
