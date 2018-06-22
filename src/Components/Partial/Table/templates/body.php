<?php
/**
 * @var \tiFy\Partial\TemplateController $this
 */
?>

<div class="tiFyPartial-TableBody">
<?php if ($datas = $this->get('datas', [])) : ?>
    <?php $num = 0; foreach ($datas as $row => $dr) : ?>
    <div class="tiFyPartial-TableBodyTr tiFyPartial-TableBodyTr--<?php echo $row; ?> tiFyPartial-TableTr tiFyPartial-TableTr-<?php echo ($num++ % 2 === 0) ? 'even' : 'odd'; ?>">
        <?php foreach ($this->get('columns', []) as $name => $label) : ?>
        <div class="tiFyPartial-TableCell<?php echo $count; ?> tiFyPartial-TableBodyTd tiFyPartial-TableBodyTd--<?php echo $name; ?> tiFyPartial-TableTd">
            <span class="tiFyPartial-TableCell-label"><?php echo $label; ?></span>
            <?php echo $dr[$name];?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
<?php else : ?>
    <div class="tiFyPartial-TableBodyTr tiFyPartial-TableBodyTr--empty tiFyPartial-TableTr">
        <div class="tiFyPartial-TableCell1 tiFyPartial-TableBodyTd tiFyPartial-TableBodyTd--empty tiFyPartial-TableTd">
            <?php echo $none; ?>
        </div>
    </div>
<?php endif; ?>
</div>