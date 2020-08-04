<?php
/**
 * Marqueur de champ de formulaire requis.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 * @var tiFy\Contracts\Form\FactoryField $field
 */
?>
<?php if ($required = $field->get('required.tagged')) : ?>
    <?php echo field('required', $required); ?>
<?php endif; ?>