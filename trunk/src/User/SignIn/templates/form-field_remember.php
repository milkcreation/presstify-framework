<?php
/**
 * @var tiFy\User\SignIn\SignInTemplateController $this
 */
?>

<?php if ($this->get('form.fields.remember')) : ?>
<p class="tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--remember">
    <?php tify_field_checkbox($this->get('form.fields.remember')); ?>

    <?php if ($label = $this->get('form.fields.remember.label')) : ?>
        <?php tify_field_label($label); ?>
    <?php endif; ?>
</p>
<?php endif; ?>