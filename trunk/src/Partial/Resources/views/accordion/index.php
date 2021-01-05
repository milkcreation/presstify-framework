<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 * @var tiFy\Partial\Drivers\Accordion\AccordionCollectionInterface $items
 */
?>
<?php $this->before(); ?>
<nav <?php $this->attrs(); ?>>
    <?php if($items->exists()) echo $items; ?>
</nav>
<?php $this->after();