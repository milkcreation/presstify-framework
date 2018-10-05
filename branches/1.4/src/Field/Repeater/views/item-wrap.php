<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<li class="tiFyField-RepeaterItem" data-index="<?php echo $this->get('index'); ?>" aria-control="item">
    <div class="tiFyField-RepeaterItemContent">
    <?php
        $this->insert(
            'item',
            [
                'index' => $this->get('index'),
                'name'  => $this->getName(),
                'value' => $this->get('value')
            ]
        );
    ?>
    </div>

    <a href="#<?php echo $this->get('container.attrs.id'); ?>"
       class="tiFyPartial-RepeaterItemRemove tiFy-Button--remove" aria-control="remove"
    ></a>

    <?php if ($order = $this->get('order')) : ?>
        <?php echo field('hidden', ['name' => $order, 'value' => $this->get('index')]); ?>
    <?php endif; ?>
</li>