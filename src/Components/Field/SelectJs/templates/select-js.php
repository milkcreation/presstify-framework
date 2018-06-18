<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<div <?php $this->attrs(); ?>>

    <?php $this->partial('handler', $this->all()); ?>

    <?php $this->partial('selected-items', $this->all()); ?>

    <?php $this->partial('picker-items', $this->all()); ?>

</div>

<?php $this->after(); ?>