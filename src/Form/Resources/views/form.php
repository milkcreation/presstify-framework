<?php
/**
 * Structure du formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 */
?>

<?php echo $this->before(); ?>

<?php $this->insert('notices', $this->all()); ?>

<form <?php echo $this->attrs(); ?>>
    <?php
    echo field(
        'hidden',
        [
            'name' => '_token',
            'value' => $this->form()->csrf(),
            'attrs' => [
                'id' => '',
                'class' => ''
            ]
        ]
    );
    ?>

    <header class="Form-header Form-header--<?php echo $this->form()->name(); ?>">
        <?php $this->insert('header', $this->all()); ?>
    </header>

    <section class="Form-body Form-body--<?php echo $this->form()->name(); ?>">
        <?php $this->insert('body', $this->all()); ?>
    </section>

    <footer class="Form-footer Form-footer--<?php echo $this->form()->name(); ?>">
        <?php $this->insert('footer', $this->all()); ?>
    </footer>
</form>

<?php echo $this->after(); ?>