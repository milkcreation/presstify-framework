<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<?php echo field('text', [
    'attrs' => $this->get('attrs', [])
]); ?>

<?php echo field('hidden', [
    'attrs' => [
        'name'         => $this->getName(),
        'data-control' => 'suggest.altfield',
    ],
]); ?>
