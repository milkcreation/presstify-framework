<?php
/**
 * Liste des boutons du formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FormView $this
 * @var tiFy\Contracts\Form\ButtonController[] $buttons
 */
?>

<?php foreach($buttons as $button) : ?>
    <?php $this->insert('button', compact('button')); ?>
<?php endforeach; ?>
