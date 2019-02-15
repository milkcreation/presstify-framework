<?php
/**
 * @var tiFy\User\SignIn\SignInViewController $this
 */
?>

<?php if ($this->get('form.fields.remember')) : ?>
<p class="tiFySignIn-Part tiFySignIn-FormFieldContainer tiFySignIn-FormFieldContainer--remember">
    <?php echo field('checkbox', $this->get('form.fields.remember')); ?>

    <?php if ($label = $this->get('form.fields.remember.label')) : ?>
        <?php echo field('label', $label); ?>
    <?php endif; ?>
</p>
<?php endif; ?>