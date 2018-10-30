<?php
/**
 * Message de succès.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 * @var string[] $messages
 */
?>

<ol class="Form-successItems">
    <?php foreach($messages as $message) : ?>
    <li class="Form-successItem"><?php echo $message; ?></li>
    <?php endforeach; ?>
</ol>