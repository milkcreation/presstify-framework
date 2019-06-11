<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<div class="tiFyPartial-tableFoot">
    <div class="tiFyPartial-tableFootTr tiFyPartial-tableTr">
    <?php foreach ($this->get('columns', [])  as $name => $label) : ?>
        <div class="tiFyPartial-tableCell<?php echo $this->get('count'); ?> tiFyPartial-tableFootTh tiFyPartial-tableFootTh--<?php echo $name; ?> tiFyPartial-tableTh tiFyPartial-tableTh--<?php echo $name; ?>">
            <?php echo $label; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>

