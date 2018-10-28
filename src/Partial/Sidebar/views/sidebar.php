<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<?php $this->before(); ?>

<div <?php echo $this->attrs(); ?>>

    <?php !$this->get('toggle') ? : $this->insert('toggle', $this->all()); ?>

    <div class="tiFyPartial-SidebarPanel">

        <?php !$this->get('header') ? : $this->insert('header', $this->all()); ?>

        <div class="tiFyPartial-SidebarBody">
            <?php if ($items = $this->get('items', [])) : ?>
                <ul class="tiFyPartial-SidebarItems">
                <?php foreach($items as $item) : ?>
                    <li <?php echo $this->htmlAttrs($item->get('attrs')); ?>><?php echo $item; ?></li>
                <?php endforeach;?>
                </ul>
            <?php endif; ?>
        </div>

        <?php !$this->get('footer') ? : $this->insert('footer', $this->all()); ?>
    </div>
</div>

<?php $this->after(); ?>