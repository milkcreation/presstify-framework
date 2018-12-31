<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>
<?php $this->before(); ?>

<?php
echo partial(
    'notice',
    [
        'attrs'   => $attrs,
        'content' => $content . $accept,
        'dismiss' => $dismiss
    ]
);
?>

<?php $this->after();