<?php
/**
 * @var tiFy\Partial\PartialViewTemplate $this
 */
?>

<?php echo $this->get('backdrop_close', ''); ?>

<div class="modal-dialog <?php echo $this->get('size'); ?>" role="document">
    <div class="modal-content">
        <?php echo $this->get('header', ''); ?>
        <?php echo $this->get('body', ''); ?>
        <?php echo $this->get('footer', ''); ?>
    </div>
</div>
