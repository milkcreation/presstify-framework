<?php
/**
 * Pied de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\ListTableViewController $this
 */
?>
<tfoot>
    <tr>
        <?php echo join('', $this->getHeaderColumns(false)); ?>
    </tr>
</tfoot>
