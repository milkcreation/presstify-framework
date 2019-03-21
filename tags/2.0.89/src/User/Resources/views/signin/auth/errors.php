<?php
/**
 * Formulaire d'authentification | Notifications > liste des erreurs.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\User\Signin\SigninView $this
 * @var array $errors
 */
?>
<ol>
    <?php foreach ($errors as $error) : ?>
        <li><?php echo $error; ?></li>
    <?php endforeach; ?>
</ol>