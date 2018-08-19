<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<nav <?php $this->attrs(); ?>>
    <ul class="tiFyField-RadioCollectionItems">
    <?php foreach($this->get('items', []) as $item) : echo $item; endforeach; ?>
    </ul>
</nav>

<?php $this->after(); ?>
