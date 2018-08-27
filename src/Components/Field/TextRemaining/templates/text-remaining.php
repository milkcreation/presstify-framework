<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<div <?php echo $this->htmlAttrs($this->get('container.attrs', [])); ?>>
    <?php $this->insert('input', $this->all()); ?>

    <?php $this->insert('infos', $this->all()); ?>
</div>

<?php $this->after(); ?>