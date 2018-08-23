<?php
/**
 * @var tiFy\Partial\TemplateController $this
 */
?>

<div class="tiFyPartial-TableHead">
    <div class="tiFyPartial-TableHeadTr tiFyPartial-TableTr">
    <?php foreach ($this->get('columns', []) as $name => $label) : ?>
        <div class="tiFyPartial-TableCell<?php echo $this->get('count'); ?> tiFyPartial-TableHeadTh tiFyPartial-TableHeadTh--<?php echo $name; ?> tiFyPartial-TableTh tiFyPartial-TableTh--<?php echo $name; ?>">
            <?php echo $label;?>
        </div>
    <?php endforeach; ?>
    </div>
</div>
