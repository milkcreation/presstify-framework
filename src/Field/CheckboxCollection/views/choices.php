<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<ul class="FieldCheckboxCollection-choices">
    <?php foreach ($this->get('items', []) as $item) : ?>
    <li class="FieldCheckboxCollection-choice">
        <?php echo $item;?>
    </li>
    <?php endforeach; ?>
</ul>