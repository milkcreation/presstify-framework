<?php
/**
 * @var tiFy\Mail\MailableView $this
 */
?>
<?php $this->layout('html/layout', $this->all()); ?>

<?php $this->start('header'); ?>
    <?php echo $this->get('header'); ?>
<?php $this->end(); ?>

<?php $this->start('footer'); ?>
    <?php echo $this->get('footer'); ?>
<?php $this->end(); ?>

<?php echo $this->get('body');