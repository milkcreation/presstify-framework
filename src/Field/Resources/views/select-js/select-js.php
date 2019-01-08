<?php
/**
 * @var tiFy\Field\FieldView $this
 * @var tiFy\Field\Fields\SelectJs\SelectJsChoices $choices
 * @var tiFy\Field\Fields\Select\SelectChoice $choice
 */
?>
<?php $this->before(); ?>

<div <?php $this->attrs(); ?>>
    <?php echo field('select', $this->get('handler', [])); ?>

    <ul data-control="select-js.selection">
        <?php foreach($choices as $choice) : ?>
        <li data-control="select-js.selection.item" data-value="<?php echo $choice->getValue(); ?>">
            <?php echo $choice->get('selection'); ?>
        </li>
        <?php endforeach; ?>
    </ul>

    <div data-control="select-js.picker">
        <ul data-control="select-js.picker.items">
        <?php foreach($choices as $choice) : ?>
            <li data-control="select-js.picker.item" data-value="<?php echo $choice->getValue(); ?>">
                <?php echo $choice->get('picker'); ?>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php $this->after();