<?php

namespace tiFy\Components\Partial\Slider;

use tiFy\Kernel\Walker\WalkerBaseController;

class SliderWalker extends WalkerBaseController
{
    /**
     * {@inheritdoc}
     */
    public function openItems($item)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function closeItems($item)
    {
        return '';
    }
}