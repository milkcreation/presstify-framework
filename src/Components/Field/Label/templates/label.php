<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<?php
echo tify_partial_tag(
    [
        'tag'       => 'label',
        'attrs'     => $this->get('attrs', []),
        'content'   => $this->get('content')
    ]
);
?>

<?php $this->after(); ?>