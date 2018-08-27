<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

    <select <?php $this->attrs(); ?>>
        <?php $this->options(); ?>
    </select>

<?php $this->after(); ?>