<?php
/**
 * @var tiFy\Partial\TemplateController $this
 */
?>

<?php
tify_partial_notice(
    [
        'attrs'   => $attrs,
        'content' => $content . $accept,
        'dismiss' => $dismiss
    ]
);