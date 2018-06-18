<?php
/**
 * @var \tiFy\Partial\TemplateController $this
 */
?>

<?php $this->before(); ?>

<<?php echo $this->get('tag'); ?> <?php echo $this->htmlAttrs($this->get('attrs', [])); ?>
<?php if ($this->get('singleton')) : ?>
    />
<?php else : ?>
    ><?php echo is_callable($this->get('content')) ? call_user_func($this->get('content')) : $this->get('content'); ?></<?php echo $this->get('tag'); ?>>
<?php endif; ?>

<?php $this->after(); ?>