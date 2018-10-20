<?php
/**
 * Liste des champs du formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FormView $this
 */
?>

<?php foreach($this->get('fields', []) as $field) : ?>
    <?php $this->insert('field', compact('field')); ?>
<?php endforeach; ?>
