<?php
/**
 * @var tiFy\Partial\PartialView $this
 * @var tiFy\Partial\Partials\Accordion\AccordionItems $items
 */
?>

<nav <?php $this->attrs(); ?>>
    <?php if($items->exists()) echo $items; ?>
</nav>