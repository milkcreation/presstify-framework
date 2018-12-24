<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<div class="tiFyPartial-TableFoot">
    <div class="tiFyPartial-TableFootTr tiFyPartial-TableTr">
    <?php foreach ($this->get('columns', [])  as $name => $label) : ?>
        <div class="tiFyPartial-TableCell<?php echo $this->get('count'); ?> tiFyPartial-TableFootTh tiFyPartial-TableFootTh--<?php echo $name; ?> tiFyPartial-TableTh tiFyPartial-TableTh--<?php echo $name; ?>">
            <?php echo $label; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>

