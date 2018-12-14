<?php
/**
 * Interface de pagination de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\ListTableViewController $this
 * @var string $which top|bottom.
 */
?>
<div class="<?php echo $this->pagination()->getClass(); ?>">
    <span class="displaying-num">
        <?php
        printf(
            _n(
                '%s élément',
                '%s éléments',
                $this->pagination()->getTotalItems(),
                'tify'
            ),
            number_format_i18n($this->pagination()->getTotalItems())
        );
        ?>
    </span>
    <span class="pagination-links<?php echo $this->pagination()->isInfiniteScroll() ? ' hide-if-js': ''; ?>">
        <?php echo $this->pagination()->which($which); ?>
    </span>
</div>
