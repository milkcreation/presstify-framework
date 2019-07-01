<?php
/**
 * Pagination - Lien vers la dernière page.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\Partials\Pagination\PaginationView $this
 */
?>

<?php if ($this->getPage() < $this->getTotalPage()) : ?>
    <li class="PartialPagination-item PartialPagination-item--last">
        <?php echo partial('tag', $this->get('links.last')); ?>
    </li>
<?php endif;