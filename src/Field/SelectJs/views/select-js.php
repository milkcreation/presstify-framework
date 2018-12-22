<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>

<div <?php $this->attrs(); ?>>
    <?php echo field('select', $this->get('handler', [])); ?>
</div>

<?php $this->after(); ?>