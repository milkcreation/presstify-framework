<?php
/**
 * @var tiFy\Partial\PartialViewTemplate $this
 */
?>

<?php $this->before(); ?>

<div <?php $this->attrs(); ?>>
    <?php echo $this->get('dialog'); ?>
</div>

<?php $this->after(); ?>
