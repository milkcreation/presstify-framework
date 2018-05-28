<?php
/**
 * @var \tiFy\Kernel\Templates\Template $this Controleur de template.
 */
?>

<div <?php echo $this->htmlAttrs($this->get('container.attrs', []));?>>
    <ul class="tiFyField-RepeaterItems" aria-control="items">
        <?php foreach($this->get('value', []) as $index => $value) : ?>
        <li class="tiFyField-RepeaterItem" data-index="<?php echo $index; ?>" aria-control="item">
            <div class="tiFyField-RepeaterItemContent">
                <?php $this->insert('item', array_merge($this->data(), ['index' => $index, 'value' => $value])); ?>
            </div>

            <a href="#<?php echo $this->get('container.attrs.id'); ?>" class="tiFyPartial-RepeaterItemRemove tify_button_remove" aria-control="remove"></a>
            <?php if ($order = $this->get('order')) : ?>
                <?php tify_field_hidden(['name' => $order, 'value' => $index]); ?>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php tify_partial_tag($this->get('button')); ?>
</div>
