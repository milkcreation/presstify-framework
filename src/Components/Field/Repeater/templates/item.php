<?php
/**
 * @var string $name
 * @var string $index
 * @var string $value
 * @var \tiFy\Field\TemplateController $this Controleur de template.
 */
?>

<?php
    tify_field_text(
        [
            'name' =>  "{$name}[" . (!is_numeric($index) ? $index : uniqid()) ."]",
            'value' => $value,
            'attrs' => [
                'class' => 'widefat'
            ]
        ]
    );