<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<li class="tiFyField-CheckboxCollectionItem">
    <?php echo field('checkbox', $this->get('checkbox', [])); ?>

    <?php echo field('label', $this->get('label', [])); ?>
</li>