<?php
/**
 * @var tiFy\Partial\Drivers\Pagination\PaginationView $this
 */
?>
<?php if ($this->getCurrentPage() > 1) : ?>
    <li class="Pagination-item Pagination-item--previous">
        <?php echo partial('tag', $this->get('links.previous')); ?>
    </li>
<?php endif;