<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<div <?php echo $this->htmlAttrs($this->get('container.attrs', [])); ?>>
    <?php $this->partial('input', $this->all()); ?>

    <?php $this->partial('infos', $this->all()); ?>
</div>

<?php $this->after(); ?>