<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php $this->before(); ?>

<?php
    echo tify_partial_tag(
        [
            'tag'       => 'input',
            'attrs'     => $this->get('attrs', [])
        ]
    );
?>

<?php $this->after(); ?>
