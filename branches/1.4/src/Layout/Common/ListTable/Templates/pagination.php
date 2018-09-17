<?php
/**
 * Interface de pagination de la table.
 *
 * @var tiFy\Components\Layout\ListTable\TemplateController $this
 * @var string $which top|bottom.
 */
?>

<div class="<?php echo $this->pagination()->getClass(); ?>">
    <span class="displaying-num">
        <?php printf(_n('%s élément', '%s éléments', $this->pagination()->getTotalItems()), number_format_i18n($this->pagination()->getTotalItems()), 'tify'); ?>
    </span>
    <span class="pagination-links<?php $this->pagination()->isInfiniteScroll() ? ' hide-if-js': ''; ?>">
        <?php echo join("\n", $this->pagination()->getPageLinks($which)); ?>
    </span>
</div>
