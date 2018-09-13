<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php $this->before(); ?>

<?php
echo field(
    'hidden',
    [
        'name'  => $this->getName(),
        'attrs' => $this->get('attrs', []),
        'value' => $this->getValue(),
    ]
);
?>

<?php $this->after();