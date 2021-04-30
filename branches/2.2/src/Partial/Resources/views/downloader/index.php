<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 */
?>
<?php $this->before(); ?>
<?php echo partial('tag', $this->get('trigger', [])); ?>
<?php $this->after();