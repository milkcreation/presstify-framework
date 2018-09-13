<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php
echo field(
    'text',
    [
        'name'  => "{$this->getName()}[" . (!is_numeric($index) ? $index : uniqid()) . "]",
        'value' => $this->getValue(),
        'attrs' => [
            'class' => 'widefat',
        ],
    ]
);