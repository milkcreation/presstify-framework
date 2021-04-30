<?php
/**
 * @var tiFy\Partial\PartialViewInterface $this
 */
echo partial('tag', [
    'tag'     => $this->get('tag'),
    'attrs'   => $this->get('attrs'),
    'content' => $this->get('content'),
]);