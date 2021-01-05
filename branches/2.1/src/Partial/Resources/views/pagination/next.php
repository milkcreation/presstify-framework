<?php
/**
 * @var tiFy\Partial\Drivers\Pagination\PaginationView $this
 */
?>
<?php if ($this->getCurrentPage() < $this->getLastPage()) : ?>
    <li class="Pagination-item Pagination-item--next">
        <?php echo partial('tag', $this->get('links.next')); ?>
    </li>
<?php endif;