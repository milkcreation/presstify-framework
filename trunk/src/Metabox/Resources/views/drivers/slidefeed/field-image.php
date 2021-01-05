<?php
/**
 * @var tiFy\Metabox\MetaboxViewInterface $this
 */
echo field('media-image', [
    'height' => 150,
    'infos'  => false,
    'name'   => $this->get('name') . '[image]',
    'value'  => $this->get('value.image'),
    'size'   => 'thumbnail',
    'width'  => 150
]);