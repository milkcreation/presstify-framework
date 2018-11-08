<?php
/**
 * Interface de navigation de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Layout\Share\ListTable\ListTableViewController $this.
 * @var string $which top|bottom.
 */
?>

<div class="tablenav <?php echo esc_attr($which); ?>">
    <?php if ($this->hasItems()) : ?>
        <div class="alignleft actions bulkactions">
            <?php echo $this->getBulkActions($which); ?>
        </div>
    <?php endif;?>

    <?php $this->insert('pagination', compact('which')); ?>

    <br class="clear" />
</div>
