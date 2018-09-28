<?php
/**
 * Vue ListTable.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Layout\Share\ListTable\ListTableViewController $this.
 */
?>

<div class="wrap">
    <?php $this->insert('header'); ?>

    <?php $this->insert('view-filters'); ?>

    <form method="get" action="">
        <?php $this->insert('search-box'); ?>

        <?php $this->insert('table'); ?>
    </form>
</div>