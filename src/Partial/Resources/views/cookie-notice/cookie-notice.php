<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<?php
echo partial(
    'notice',
    [
        'attrs'   => $attrs,
        'content' => $content . $accept,
        'dismiss' => $dismiss
    ]
);