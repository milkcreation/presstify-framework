<?php

/**
 * @var tiFy\Metabox\MetaboxViewInterface $this
 */
echo field(
    'select-image',
    array_merge(
        $this->all(),
        [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
        ]
    )
);