<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<div <?php $this->attrs(); ?>>

    <?php $this->insert('handler', $this->all()); ?>

    <?php $this->insert('selected-items', $this->all()); ?>

    <?php $this->insert('picker-items', $this->all()); ?>

</div>

<?php $this->after(); ?>