<?php
/**
 * @var tiFy\User\SignIn\SignInTemplateController $this
 */
?>

<p class="tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--password">
    <?php if ($label = $this->get('form.fields.password.label')) : ?>
        <?php tify_field_label($label); ?>
    <?php endif; ?>

    <?php tify_field_password($this->get('form.fields.password')); ?>
</p>
