<?php

/**
 * @var tiFy\Metabox\MetaboxViewInterface $this
 */
echo field(
    'colorpicker',
    array_merge(
        $this->all(),
        [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
        ]
    )
);