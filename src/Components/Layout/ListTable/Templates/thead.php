<?php
/**
 * Entête de la table.
 *
 * @var tiFy\Components\Layout\ListTable\TemplateController $this
 */
?>

<thead>
    <tr>
        <?php echo join('', $this->getHeaderColumns()); ?>
    </tr>
</thead>
