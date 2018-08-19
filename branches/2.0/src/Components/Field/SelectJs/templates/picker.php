<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<div id="tiFyField-SelectJsPicker--<?php echo $this->getId(); ?>" class="tiFyField-SelectJsPicker">
    <ul id="tiFyField-SelectJsPickerItems--<?php echo $this->getId(); ?>" class="tiFyField-SelectJsPickerItems">
    <?php foreach($this->get('picker_items', []) as $item) : ?>
        <?php $this->insert('picker-item', $item->all()); ?>
    <?php endforeach; ?>
    </ul>
</div>
