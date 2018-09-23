<?php
/**
 * @var tiFy\User\SignIn\SignInTemplateController $this
 */
?>

<?php if ($this->get('form.lost_password_link')) :?>
    <?php echo $this->lostpasswordLink(); ?>
<?php endif; ?>
