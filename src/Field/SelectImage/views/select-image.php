<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>

<?php
echo field(
    'select-js',
    [
        'name'      => $this->getName(),
        'value'     => $this->getValue(),
        'attrs'     => $this->get('attrs', []),
        'options'   => $this->get('options', []),
        'removable' => false,
    ]
);
?>

<?php $this->after();