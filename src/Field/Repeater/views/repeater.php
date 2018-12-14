<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>

<div <?php $this->attrs() ?>>
    <ul class="tiFyField-RepeaterItems" aria-control="items">
        <?php foreach ($this->get('value', []) as $index => $value) : ?>
            <?php
            $this->insert(
                'item-wrap',
                [
                    'index' => $index,
                    'name'  => $this->get('name'),
                    'order' => $this->get('order'),
                    'value' => $value,
                    'args'  => $this->get('args', []),
                ]
            );
            ?>
        <?php endforeach; ?>
    </ul>

    <?php $this->insert('button', $this->all()); ?>
</div>

<?php $this->after();