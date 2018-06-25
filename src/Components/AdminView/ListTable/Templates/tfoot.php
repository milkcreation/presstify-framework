<?php
/**
 * Pied de la table.
 *
 * @var tiFy\Components\AdminView\ListTable\TemplateController $this
 */
?>

<tfoot>
    <tr>
        <?php echo join('', $this->getHeaderColumns(false)); ?>
    </tr>
</tfoot>
