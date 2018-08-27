<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<div id="tiFyField-SelectJsTrigger--<?php echo $this->getId(); ?>" class="tiFyField-SelectJsTrigger">
    <ul id="tiFyField-SelectJsSelectedItems--<?php echo $this->getId(); ?>"
        class="tiFyField-SelectJsSelectedItems">
        <?php foreach($this->get('selected_items', []) as $item) : ?>
            <?php $this->insert('selected-item', $item->all()); ?>
        <?php endforeach; ?>
    </ul>
</div>
