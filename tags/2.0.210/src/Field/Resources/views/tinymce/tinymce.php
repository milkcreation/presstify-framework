<?php
/**
 * @var tiFy\Field\FieldView $this
 */
?>
<?php $this->before(); ?>
<?php echo partial('tag', [
    'tag'     => 'textarea',
    'attrs'   => $this->get('attrs', []),
    'content' => $this->get('value', ''),
]); ?>
<?php $this->after();