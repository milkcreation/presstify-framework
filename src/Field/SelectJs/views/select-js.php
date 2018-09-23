<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>

<div <?php $this->attrs(); ?>>
    <?php $this->insert('handler', $this->all()); ?>
    <?php $this->insert('selected', $this->all()); ?>
    <?php $this->insert('picker', $this->all()); ?>
</div>

<?php $this->after(); ?>