<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>

<?php
echo partial(
    'tag',
    [
        'tag'     => 'span',
        'attrs'   => $this->get('infos_area.attrs', []),
        'content' => '',
    ]
);