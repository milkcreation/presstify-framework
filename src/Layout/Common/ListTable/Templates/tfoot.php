<?php
/**
 * Pied de la table.
 *
 * @var tiFy\Components\Layout\ListTable\TemplateController $this
 */
?>

<tfoot>
    <tr>
        <?php echo join('', $this->getHeaderColumns(false)); ?>
    </tr>
</tfoot>
