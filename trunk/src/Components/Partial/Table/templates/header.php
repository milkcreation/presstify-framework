<?php
/**
 * @var array $columns
 */
?>

<div class="tiFyPartial-TableHead">
    <div class="tiFyPartial-TableHeadTr tiFyPartial-TableTr">
    <?php foreach ($columns as $name => $label) : ?>
        <div class="tiFyPartial-TableCell<?php echo $count; ?> tiFyPartial-TableHeadTh tiFyPartial-TableHeadTh--<?php echo $name; ?> tiFyPartial-TableTh tiFyPartial-TableTh--<?php echo $name; ?>">
            <?php echo $label;?>
        </div>
    <?php endforeach; ?>
    </div>
</div>
