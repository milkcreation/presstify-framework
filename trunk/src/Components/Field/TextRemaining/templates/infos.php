<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php
    echo tify_partial_tag(
        [
            'tag'       => 'span',
            'attrs'     => $this->get('infos_area.attrs', []),
            'content'   => ''
        ]
    );
?>
