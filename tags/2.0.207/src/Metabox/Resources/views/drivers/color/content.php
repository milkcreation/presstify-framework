<?php
/**
 * @var tiFy\Contracts\Metabox\MetaboxView $this
 */
echo field('colorpicker', [
    'name'    => $this->get('name'),
    'value'   => $this->get('value', '')
]);