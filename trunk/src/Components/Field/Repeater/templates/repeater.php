<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<div <?php echo $this->htmlAttrs($this->get('container.attrs', []));?>>
    <ul class="tiFyField-RepeaterItems" aria-control="items">
        <?php foreach($this->get('value', []) as $index => $value) : ?>
        <?php
            $this->partial(
                'item-wrap',
                [
                    'index' => $index,
                    'name'  => $this->get('name'),
                    'order' => $this->get('order'),
                    'value' => $value
                ]
            );
        ?>
        <?php endforeach; ?>
    </ul>

    <?php $this->partial('button', $this->all()); ?>
</div>

<?php $this->after(); ?>
