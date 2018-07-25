<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<?php
    tify_field_hidden(
        [
            'name' => $this->getName(),
            'attrs' => $this->get('attrs', []),
            'value' => $this->getValue()
        ]
    );
?>

<?php $this->after(); ?>
