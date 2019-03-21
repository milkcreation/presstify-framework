<?php
/**
 * Formulaire d'authentification | Notifications > liste des informations.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\User\Signin\SigninView $this
 * @var array $infos
 */
?>
<ol>
    <?php foreach($infos as $info) : ?>
        <li><?php echo $info; ?></li>
    <?php endforeach; ?>
</ol>