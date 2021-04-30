<?php
/**
 * @var tiFy\Partial\Drivers\Pagination\PaginationView $this
 */
?>
<?php if ($this->getCurrentPage() > 1) : ?>
    <li class="Pagination-item Pagination-item--first">
        <?php echo partial('tag', $this->get('links.first')); ?>
    </li>
<?php endif;