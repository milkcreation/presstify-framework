<?php
/**
 * @var tiFy\Partial\PartialViewTemplate $this
 */
?>

<?php
tify_partial_tag(
    [
        'tag'     => 'div',
        'attrs'   => $this->get('attrs', []),
        'content' => $this->get('content', '') . $this->get('dismiss', '')
    ]
);
