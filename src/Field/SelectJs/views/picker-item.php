<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<li data-label="<?php echo $this->e($this->get('content', '')); ?>"
    data-value="<?php echo $this->get('value', ''); ?>"
    data-index="<?php echo $index; ?>"
    aria-disabled="<?php echo $disabled; ?>"
>
    <?php echo $content; ?>
</li>
