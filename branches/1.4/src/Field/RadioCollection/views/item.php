<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<li class="tiFyField-RadioCollectionItem">
    <?php echo field('radio', $this->get('radio', [])); ?>

    <?php echo field('label', $this->get('label', [])); ?>
</li>