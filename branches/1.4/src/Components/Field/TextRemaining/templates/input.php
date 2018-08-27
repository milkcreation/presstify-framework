<?php
/**
 * @var \tiFy\Field\TemplateController $this
 */
?>

<?php echo tify_partial_tag(
        [
            'tag'       => $this->get('tag'),
            'content'   => $this->get('content'),
            'attrs'     => $this->get('attrs')
        ]
    );
?>