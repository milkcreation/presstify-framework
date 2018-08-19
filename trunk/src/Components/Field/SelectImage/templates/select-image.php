<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<?php
tify_field_select_js(
    [
        'name'      => $this->getName(),
        'value'     => $this->getValue(),
        'attrs'     => $this->get('attrs'),
        'options'   => $this->get('options'),
        'removable' => false
    ]
);
?>

<?php $this->after(); ?>