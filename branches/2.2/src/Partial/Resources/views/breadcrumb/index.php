<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 */
?>
<?php $this->before(); ?>
<?php if($parts = $this->get('parts', [])) : ?>
<ol <?php $this->attrs(); ?>>
    <?php foreach ($parts as $part) : echo $part; endforeach;?>
</ol>
<?php endif; ?>
<?php $this->after();