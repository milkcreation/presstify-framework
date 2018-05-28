<?php
/**
 * @var \tiFy\Kernel\Templates\Template $this Controleur de template.
 */
?>

<?php
tify_field_text(
    [
        'name' =>  "{$name}[{$index}]",
        'value' => $value,
        'attrs' => [
            'class' => 'widefat'
        ]
    ]
);
?>
