<?php
/**
 * Interface de navigation de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\ListTableViewController $this
 * @var string $which top|bottom.
 */
?>
<div class="tablenav <?php echo esc_attr($which); ?>">

    <?php $this->insert('bulk-actions', compact('which')); ?>

    <?php $this->insert('pagination', compact('which')); ?>

    <br class="clear" />
</div>
