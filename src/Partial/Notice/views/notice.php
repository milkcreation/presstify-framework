<?php
/**
 * @var tiFy\Partial\PartialView $this
 */
?>

<?php
echo partial(
    'tag',
    [
        'tag'     => 'div',
        'attrs'   => $this->get('attrs', []),
        'content' => $this->get('content', '') . $this->get('dismiss', '')
    ]
);
