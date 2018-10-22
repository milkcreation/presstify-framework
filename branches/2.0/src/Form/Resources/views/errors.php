<?php
/**
 * Liste des messages d'erreurs.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FormView $this
 * @var string[] $errors
 */
?>

<ol class="Form-errors">
    <?php foreach($errors as $error) : ?>
    <li class="Form-error"><?php echo $error; ?></li>
    <?php endforeach; ?>
</ol>