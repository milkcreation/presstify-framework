<?php

namespace tiFy\Components\Partial\SlickCarousel;

use tiFy\Kernel\Walker\WalkerBaseController;

class SlickCarouselWalker extends WalkerBaseController
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