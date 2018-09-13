<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<ol <?php $this->attrs(); ?>>
    <?php foreach ($this->get('items', []) as $item) : echo $item; endforeach;?>
</ol>