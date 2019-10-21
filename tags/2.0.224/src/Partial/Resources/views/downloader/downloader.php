<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>
<?php echo $this->before(); ?>
<?php echo partial('tag', $this->get('trigger', [])); ?>
<?php echo $this->after();