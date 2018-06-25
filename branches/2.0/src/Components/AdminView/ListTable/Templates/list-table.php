<?php
/**
 * Vue ListTable.
 *
 * @var tiFy\Components\AdminView\ListTable\TemplateController $this
 */
?>

<div class="wrap">
    <?php $this->partial('header'); ?>

    <?php $this->partial('view-filters'); ?>

    <form method="get" action="">
        <?php $this->partial('search-box'); ?>

        <?php $this->partial('table'); ?>
    </form>
</div>
