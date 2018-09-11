<?php
/**
 * @var tiFy\User\SignIn\SignInTemplateController $this
 */
?>

<?php echo $this->formBefore(); ?>

<form <?php $this->attrs(); ?>>

    <?php
        tify_field_hidden(
            [
                'name' => 'tiFySignIn',
                'value' => $this->get('name')
            ]
        );
    ?>

    <?php
        tify_field_hidden(
            [
                'name' => '_wpnonce',
                'value' => \wp_create_nonce('tiFySignIn-in-' . $this->get('name'))
            ]
        );
    ?>

    <?php echo $this->formHeader(); ?>

    <?php echo $this->formBody(); ?>

    <?php echo $this->formFooter(); ?>

</form>

<?php echo $this->formAfter(); ?>