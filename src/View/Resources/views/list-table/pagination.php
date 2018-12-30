<?php
/**
 * Interface de pagination de la table.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\View\Pattern\ListTable\Viewer\Viewer $this
 * @var string $which top|bottom.
 */
?>
<?php $pagination = $this->pagination()->which($which); ?>

<div class="<?php echo $pagination->getClass(); ?>">
    <span class="displaying-num">
        <?php
        printf(
            _n(
                '%s élément',
                '%s éléments',
                $this->pagination()->getTotalItems(),
                'tify'
            ),
            number_format_i18n($pagination->getTotalItems())
        );
        ?>
    </span>

    <span class="pagination-links<?php echo $pagination->isInfiniteScroll() ? ' hide-if-js': ''; ?>">
        <?php echo $pagination->firstPage(); ?>

        <?php echo $pagination->prevPage(); ?>

        <?php echo $pagination->currentPage(); ?>

        <?php echo $pagination->nextPage(); ?>

        <?php echo $pagination->lastPage(); ?>
    </span>
</div>
