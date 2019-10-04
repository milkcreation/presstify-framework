<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
echo partial('tag', [
    'attrs'   => [
        'data-control' => 'metabox-videofeed.add',
    ],
    'content' => _n(
        __('Ajouter une video', 'tify'),
        __('Ajouter des vidéos', 'tify'),
        (($this->get('max', -1) === 1) ? 1 : 2),
        'tify'
    ),
    'tag'     => 'button',
]);