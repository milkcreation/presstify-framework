<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<div <?php $this->attrs(); ?>>
    <span class="tiFyPartial-HolderImageSizer"
          style="padding-top:<?php echo ceil((100/$this->get('width'))* $this->get('height')); ?>%"
    ></span>
    <div class="tiFyPartial-HolderImageContent"><?php echo $this->get('content', ''); ?></div>
</div>
