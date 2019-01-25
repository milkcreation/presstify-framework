<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>
<?php $this->before(); ?>

<div <?php $this->attrs(); ?>>
    <?php echo $this->get('items', ''); ?>
</div>

<?php $this->after();