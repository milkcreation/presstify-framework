<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>
<?php $this->before(); ?>
    <<?php echo $this->get('tag'); ?> <?php echo $this->htmlAttrs(); ?>>
    <span class="hamburger-box"><span class="hamburger-inner"></span></span>
    </<?php echo $this->get('tag'); ?>>
<?php $this->after();