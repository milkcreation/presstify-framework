<?php
/**
 * Pagination - Liste des numéros de page.
 * ---------------------------------------------------------------------------------------------------------------------
 * @var tiFy\Partial\PartialView $this
 */
?>

<?php if ($this->get('numbers')) : ?>
    <?php if ($this->get('left_gap') && !$this->get('right_gap')) : ?>

        self::loop(1, $this-get('anchor'), 0);

        <?php $this->insert('ellipis', $this->all()); ?>

        self::loop($block_min, $total, $paged));

    <?php elseif ($this->get('left_gap') && $this->get('right_gap')) : ?>

        self::loop(1, $anchor, 0);

        <?php $this->insert('ellipis', $this->all()); ?>

        self::loop($block_min, $block_high, $paged);

        <?php $this->insert('ellipis', $this->all()); ?>

        self::loop(($total - $anchor + 1), $total);

    <?php elseif (!$this->get('left_gap') && $this->get('right_gap')) : ?>

        self::loop(1, $block_high, $paged);

        <?php $this->insert('ellipis', $this->all()); ?>

        self::loop(($total - $anchor + 1), $total));

    <?php else : ?>

        self::loop(1, $total, $paged);

    <?php endif; ?>
<?php endif;