<?php
/**
 * Point d'entrée.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 */
?>
<?php if ($this->form()->get('wrapper')) : ?>
    <?php $this->layout('wrapper-form', $this->all()); ?>
<?php endif; ?>

<?php echo $this->before(); ?>

<?php $this->insert('notices', $this->all()); ?>

<form <?php echo $this->htmlAttrs($this->form()->get('attrs', [])); ?>>
    <?php echo field('hidden', [
        'name'  => '_token',
        'value' => $this->csrf(),
        'attrs' => [
            'class' => '',
        ],
    ]); ?>

    <header class="Form-header Form-header--<?php echo $this->tagName(); ?>">
        <?php $this->insert('header', $this->all()); ?>
    </header>

    <section class="Form-body Form-body--<?php echo $this->tagName(); ?>">
        <?php $this->insert('body', $this->all()); ?>
    </section>

    <footer class="Form-footer Form-footer--<?php echo $this->tagName(); ?>">
        <?php $this->insert('footer', $this->all()); ?>
    </footer>
</form>

<?php echo $this->after();