<?php
/**
 * @var tiFy\User\SignIn\SignInTemplateController $this
 */
?>

<?php echo $this->formBefore(); ?>

    <form <?php $this->attrs(); ?>>

        <?php
        echo field(
            'hidden',
            [
                'name'  => 'tiFySignIn',
                'value' => $this->get('name'),
            ]
        );
        ?>

        <?php
        echo field(
            'hidden',
            [
                'name'  => '_wpnonce',
                'value' => \wp_create_nonce('tiFySignIn-in-' . $this->get('name')),
            ]
        );
        ?>

        <?php echo $this->formHeader(); ?>

        <?php echo $this->formBody(); ?>

        <?php echo $this->formFooter(); ?>

    </form>

<?php echo $this->formAfter(); ?>