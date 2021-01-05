<?php
/**
 * @var tiFy\Partial\Drivers\Pagination\PaginationView $this
 */
?>
<?php if ($this->getCurrentPage() < $this->getLastPage()) : ?>
    <li class="Pagination-item Pagination-item--last">
        <?php echo partial('tag', $this->get('links.last')); ?>
    </li>
<?php endif;