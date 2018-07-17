<?php
/**
 * @var tiFy\User\SignIn\SignInTemplateController $this
 */
?>

<a  href="<?php echo \wp_lostpassword_url($this->get('lost_password_link.redirect', '')); ?>\"
    title="<?php echo $this->get('lost_password_link.title', __('Récupération de mot de passe perdu', 'tify')); ?>"
    class="tiFySignIn-LostPasswordLink"
>
    <?php echo $this->get('lost_password_link.content', ''); ?>
</a>