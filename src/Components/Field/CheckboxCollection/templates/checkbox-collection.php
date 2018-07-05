<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<ul <?php $this->attrs(); ?>>
<?php foreach($this->get('items', []) as $item) : echo $item; endforeach; ?>
</ul>

<?php $this->after(); ?>
