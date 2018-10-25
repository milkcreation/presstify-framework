<?php
/**
 * Pagination - Interface.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\Pagination\PaginationView $this
 */
?>

<?php if ($this->get('total') > 1) : ?>
<ul <?php $this->attrs(); ?>>
    <?php $this->get('first') ? $this->insert('first', $this->all()) : false; ?>

    <?php $this->get('previous') ? $this->insert('previous', $this->all()) : false; ?>

    <?php $this->get('numbers') ? $this->insert('numbers', $this->all()) : false; ?>

    <?php $this->get('next') ? $this->insert('next', $this->all()) : false; ?>

    <?php $this->get('last') ? $this->insert('last', $this->all()) : false; ?>
</ul>
<?php endif; ?>