<?php
/**
 * Liste des champs du formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FormView $this
 * @var tiFy\Contracts\Form\FactoryField[] $fields
 */
?>

<?php foreach($fields as $field) : ?>
    <?php $this->insert('field', compact('field')); ?>
<?php endforeach; ?>
