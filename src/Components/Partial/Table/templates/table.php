<?php
/**
 * @var \tiFy\Partial\TemplateController $this
 */
?>

<?php $this->before(); ?>

<div class="tiFyPartial-Table">
    <?php if ($this->get('header')) $this->partial('header', $this->all()); ?>

    <?php $this->partial('body', $this->all()); ?>

    <?php if ($this->get('footer')) $this->partial('footer', $this->all()); ?>
</div>

<?php $this->after(); ?>