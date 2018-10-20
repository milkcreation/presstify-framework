<?php
/**
 * Structure du formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FormView $this
 */
?>

<form>
    <header>
        <?php $this->insert('header', $this->all()); ?>
    </header>

    <section>
        <?php $this->insert('body', $this->all()); ?>
    </section>

    <footer>
        <?php $this->insert('footer', $this->all()); ?>
    </footer>
</form>
