<?php
/**
 * Table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 */
?>
<?php $this->insert('tablenav', ['which' => 'top']); ?>

<table class="wp-list-table <?php echo $this->getTableClasses(); ?>">

    <?php $this->insert('thead'); ?>

    <?php $this->insert('tbody'); ?>

    <?php $this->insert('tfoot'); ?>

</table>

<?php $this->insert('tablenav', ['which' => 'bottom']); ?>