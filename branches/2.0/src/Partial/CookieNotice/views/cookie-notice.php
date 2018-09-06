<?php
/**
 * @var tiFy\Partial\PartialViewTemplate $this
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