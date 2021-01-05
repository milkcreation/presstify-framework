<?php

/**
 * @var tiFy\Metabox\MetaboxViewInterface $this
 */
echo field(
    'text-remaining',
    array_merge(
        $this->all(),
        [
            'name'  => $this->getName(),
            'value' => $this->getValue(),
        ]
    )
);
