<?php
/**
 * @var tiFy\Field\FieldView $this
 * @var int $index
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