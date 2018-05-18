<?php
/**
 * @var array $columns
 */
?>

<div class="tiFyPartial-TableFoot">
    <div class="tiFyPartial-TableFootTr tiFyPartial-TableTr">
    <?php foreach ($columns as $name => $label) : ?>
        <div class="tiFyPartial-TableCell<?php echo $count; ?> tiFyPartial-TableFootTh tiFyPartial-TableFootTh--<?php echo $name; ?> tiFyPartial-TableTh tiFyPartial-TableTh--<?php echo $name; ?>">
            <?php echo $label; ?>
        </div>
    <?php endforeach; ?>
    </div>
</div>

