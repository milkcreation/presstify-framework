<?php
/**
 * @var tiFy\Contracts\Form\FormView $this
 * @var tiFy\Contracts\Form\FieldDriver $field
 */
?>
<?php if ($field->hasLabel()) : ?>
    <?php if ($field->params('label.wrapper')) : $this->layout('wrapper-label', $this->all()); endif; ?>
    <?php echo field('label', $field->params('label', [])); ?>
    <?php $this->insert('field-required', compact('field')); ?>
<?php endif;