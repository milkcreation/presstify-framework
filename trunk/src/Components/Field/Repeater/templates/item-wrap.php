<?php
/**
 * @var \tiFy\Kernel\Templates\Template $this Controleur de template.
 */
?>

<li class="tiFyField-RepeaterItem" data-index="<?php echo $index; ?>" aria-control="item">
    <div class="tiFyField-RepeaterItemContent">
    <?php
        $this->partial(
            'item',
            [
                'index' => $index,
                'name'  => $name,
                'value' => $value
            ]
        );
    ?>
    </div>

    <a href="#<?php echo $this->get('container.attrs.id'); ?>" class="tiFyPartial-RepeaterItemRemove tiFy-Button--remove" aria-control="remove"></a>
    <?php if ($order) : ?>
        <?php tify_field_hidden(['name' => $order, 'value' => $index]); ?>
    <?php endif; ?>
</li>