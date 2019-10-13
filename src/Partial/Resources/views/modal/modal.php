<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>
<?php $this->before(); ?>
<div <?php $this->attrs(); ?>>
    <?php echo $this->get('backdrop_close', ''); ?>

    <div data-control="modal.dialog" class="<?php echo $this->get('size'); ?>">
        <div data-control="modal.content">
            <div data-control="modal.header">
                <?php echo $this->get('header', ''); ?>
            </div>
            <div data-control="modal.body">
                <?php echo $this->get('body', ''); ?>
            </div>
            <div data-control="modal.footer">
                <?php echo $this->get('footer', ''); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->after();