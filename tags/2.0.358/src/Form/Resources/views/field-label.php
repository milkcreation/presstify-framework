<?php
/**
 * Etiquette de champ de formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 * @var tiFy\Contracts\Form\FactoryField $field
 */
?>
<?php if ($field->hasLabel()) : ?>
    <?php if ($field->get('label.wrapper')) : $this->layout('wrapper-label', $this->all()); endif; ?>
    <?php echo field('label', $field->get('label', [])); ?>
    <?php $this->insert('field-required', compact('field')); ?>
<?php endif;