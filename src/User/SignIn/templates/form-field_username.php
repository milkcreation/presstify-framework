<?php
/**
 * @var tiFy\User\SignIn\SignInTemplateController $this
 */
?>

<p class="tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--username">
    <?php if ($label = $this->get('form.fields.username.label')) : ?>
        <?php tify_field_label($label); ?>
    <?php endif; ?>

    <?php tify_field_text($this->get('form.fields.username')); ?>
</p>
