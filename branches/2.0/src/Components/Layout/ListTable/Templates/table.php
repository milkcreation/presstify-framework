<?php
/**
 * Table.
 *
 * @var tiFy\Components\Layout\ListTable\TemplateController $this
 */
?>

<?php $this->insert('tablenav', ['which' => 'top']); ?>

<table class="wp-list-table <?php echo implode(' ', $this->getTableClasses()); ?>">

    <?php $this->insert('thead'); ?>

    <?php $this->insert('tbody'); ?>

    <?php $this->insert('tfoot'); ?>

</table>

<?php $this->insert('tablenav', ['which' => 'bottom']); ?>