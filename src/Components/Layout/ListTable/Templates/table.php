<?php
/**
 * Table.
 *
 * @var tiFy\Components\Layout\ListTable\TemplateController $this
 */
?>

<?php $this->partial('tablenav', ['which' => 'top']); ?>

<table class="wp-list-table <?php echo implode(' ', $this->getTableClasses()); ?>">

    <?php $this->partial('thead'); ?>

    <?php $this->partial('tbody'); ?>

    <?php $this->partial('tfoot'); ?>

</table>

<?php $this->partial('tablenav', ['which' => 'bottom']); ?>