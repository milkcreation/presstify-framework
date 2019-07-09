<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<?php $this->before(); ?>
<?php echo partial('tag', $this->get('container', [])); ?>
<?php $this->after();