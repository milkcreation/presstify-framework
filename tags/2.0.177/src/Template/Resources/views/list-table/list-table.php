<?php
/**
 * Vue ListTable.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Template\Templates\ListTable\Viewer $this
 */
?>
<div class="wrap">
    <?php $this->insert('header'); ?>
    <?php $this->insert('view-filters'); ?>
    <form method="get">
        <?php if ($this->search()->exists()) : ?>
            <?php $this->insert('search'); ?>
        <?php endif; ?>
        <?php $this->insert('table'); ?>
    </form>
</div>