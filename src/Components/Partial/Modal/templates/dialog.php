<?php
/**
 * @var string $backdrop_close
 * @var string $size
 * @var string $header
 * @var string $body
 * @var string $footer
 */
?>

<?php echo $backdrop_close; ?>

<div class="modal-dialog <?php echo $size; ?>" role="document">
    <div class="modal-content">
        <?php echo $header; ?>
        <?php echo $body; ?>
        <?php echo $footer; ?>
    </div>
</div>
