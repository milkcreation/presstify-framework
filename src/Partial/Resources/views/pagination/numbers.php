<?php
/**
 * @var tiFy\Partial\Drivers\Pagination\PaginationView $this
 */
?>
<?php foreach ($this->get('numbers', []) as $number) : ?>
    <li class="Pagination-item Pagination-item--num">
        <?php echo partial('tag', $number); ?>
    </li>
<?php endforeach;