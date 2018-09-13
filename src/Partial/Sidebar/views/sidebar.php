<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<?php $this->before(); ?>

<div <?php echo $this->attrs(); ?>>

    <?php echo $this->toggle(); ?>

    <div class="tiFyPartial-SidebarPanel">
        <div class="tiFyPartial-SidebarHeader">
            <?php echo $this->header(); ?>
        </div>

        <div class="tiFyPartial-SidebarBody">
            <?php if ($items = $this->get('items', [])) : ?>
                <ul class="tiFyPartial-SidebarItems">
                <?php foreach($items as $item) : ?>
                    <li <?php echo $this->htmlAttrs($item->get('attrs')); ?>><?php echo $item; ?></li>
                <?php endforeach;?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="tiFyPartial-SidebarFooter">
            <?php echo $this->footer(); ?>
        </div>
    </div>
</div>

<?php $this->after(); ?>