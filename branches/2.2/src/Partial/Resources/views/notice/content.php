<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 */
?>
<?php if (is_array($this->get('content'))) : ?>
    <ol class="Notice-items">
        <?php foreach ($this->get('content') as $item) : ?>
            <li class="Notice-item"><?php echo $item; ?></li>
        <?php endforeach; ?>
    </ol>
<?php else : ?>
    <?php echo $this->get('content'); ?>
<?php endif; ?>
