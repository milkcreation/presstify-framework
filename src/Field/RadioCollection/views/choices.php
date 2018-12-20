<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<ul class="FieldRadioCollection-choices">
    <?php foreach ($this->get('items', []) as $item) : ?>
    <li class="FieldRadioCollection-choice">
        <?php echo $item;?>
    </li>
    <?php endforeach; ?>
</ul>