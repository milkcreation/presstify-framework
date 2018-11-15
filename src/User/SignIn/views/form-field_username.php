<?php
/**
 * @var tiFy\User\SignIn\SignInViewController $this
 */
?>

<p class="tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--username">
    <?php if ($label = $this->get('form.fields.username.label')) : ?>
        <?php echo field('label', $label); ?>
    <?php endif; ?>

    <?php echo field('text', $this->get('form.fields.username')); ?>
</p>
