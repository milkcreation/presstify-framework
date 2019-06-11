<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<div class="tiFyPartial-tableHead">
    <div class="tiFyPartial-tableHeadTr tiFyPartial-tableTr">
    <?php foreach ($this->get('columns', []) as $name => $label) : ?>
        <div class="tiFyPartial-tableCell<?php echo $this->get('count'); ?> tiFyPartial-tableHeadTh tiFyPartial-tableHeadTh--<?php echo $name; ?> tiFyPartial-tableTh tiFyPartial-tableTh--<?php echo $name; ?>">
            <?php echo $label;?>
        </div>
    <?php endforeach; ?>
    </div>
</div>
