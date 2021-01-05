<?php

/**
 * @var tiFy\Metabox\MetaboxViewInterface $this
 */
echo field(
    'media-image',
    array_merge(
        $this->all(),
        [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
        ]
    )
);