<?php
/**
 * @var tiFy\User\SignIn\SignInTemplateController $this
 */
?>

<p class="tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--password">
    <?php if ($label = $this->get('form.fields.password.label')) : ?>
        <?php echo field('label', $label); ?>
    <?php endif; ?>

    <?php echo field('password', $this->get('form.fields.password')); ?>
</p>
