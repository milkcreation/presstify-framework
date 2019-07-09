<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<div class="tiFyPartial-tableBody">
<?php if ($datas = $this->get('datas', [])) : ?>
    <?php $num = 0; foreach ($datas as $row => $dr) : ?>
    <div class="tiFyPartial-tableBodyTr tiFyPartial-tableBodyTr--<?php echo $row; ?> tiFyPartial-tableTr tiFyPartial-tableTr-<?php echo ($num++ % 2 === 0) ? 'even' : 'odd'; ?>">
        <?php foreach ($this->get('columns', []) as $name => $label) : ?>
        <div class="tiFyPartial-tableCell<?php echo $count; ?> tiFyPartial-tableBodyTd tiFyPartial-tableBodyTd--<?php echo $name; ?> tiFyPartial-tableTd">
            <span class="tiFyPartial-tableCell-label"><?php echo $label; ?></span>
            <?php echo $dr[$name];?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="tiFyPartial-tableBodyTr tiFyPartial-tableBodyTr--empty tiFyPartial-tableTr">
        <div class="tiFyPartial-tableCell1 tiFyPartial-tableBodyTd tiFyPartial-tableBodyTd--empty tiFyPartial-tableTd">
            <?php echo $none; ?>
        </div>
    </div>
<?php endif; ?>
</div>