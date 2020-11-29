<?php
/**
 * Corps du formulaire.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Contracts\Form\FactoryView $this
 * @var tiFy\Contracts\Form\FactoryFields $fields
 */
?>
<?php if ($fields->exists()) : ?>
    <div class="FormFields">
        <?php $this->insert('groups', $this->all()); ?>
    </div>
<?php endif; ?>
