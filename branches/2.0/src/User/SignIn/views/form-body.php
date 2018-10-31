<?php
/**
 * @var tiFy\User\SignIn\SignInViewController $this
 */
?>

<?php echo $this->formHiddenFields(); ?>

<?php foreach (['formFieldUsername', 'formFieldPassword'] as $required_field_name) : ?>
    <?php echo $this->{$required_field_name}(); ?>
<?php endforeach; ?>

<?php echo $this->formAdditionnalFields(); ?>

<?php echo $this->formFieldRemember(); ?>

<?php echo $this->formFieldSubmit(); ?>