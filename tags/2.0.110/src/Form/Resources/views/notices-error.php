<?php
/**
 * Messages d'erreurs.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 * @var string[] $messages
 */
?>
<ol class="Form-errorItems">
    <?php foreach ($messages as $message) : ?>
        <li class="Form-errorItem"><?php echo $message; ?></li>
    <?php endforeach; ?>
</ol>