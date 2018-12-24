<?php
/**
 * Pagination - Liste des numéros de page.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\Partials\Pagination\PaginationView $this
 */
?>

<?php if ($this->get('numbers')) : ?>
    <?php if ($this->get('numbers.left_gap') && !$this->get('numbers.right_gap')) : ?>
        <?php $this->numLoop(1, $this->get('numbers.anchor')); ?>

        <?php $this->insert('num-ellipsis', $this->all()); ?>

        <?php $this->numLoop($this->get('numbers.block_min'), $this->get('total')); ?>

    <?php elseif ($this->get('numbers.left_gap') && $this->get('numbers.right_gap')) : ?>
        <?php $this->numLoop(1, $this->get('numbers.anchor')); ?>

        <?php $this->insert('num-ellipsis', $this->all()); ?>

        <?php $this->numLoop($this->get('numbers.block_min'), $this->get('numbers.block_high')); ?>

        <?php $this->insert('num-ellipsis', $this->all()); ?>

        <?php $this->numLoop(($this->get('total')-$this->get('numbers.anchor')+1), $this->get('total')); ?>

    <?php elseif (!$this->get('numbers.left_gap') && $this->get('numbers.right_gap')) : ?>
        <?php $this->numLoop(1, $this->get('numbers.block_high')); ?>

        <?php $this->insert('num-ellipsis', $this->all()); ?>

        <?php $this->numLoop(($this->get('total')-$this->get('numbers.anchor')+1), $this->get('total'));?>

    <?php else : ?>

        <?php $this->numLoop(1, $this->get('total')); ?>

    <?php endif; ?>
<?php endif;