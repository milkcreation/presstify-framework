<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers;

use tiFy\Partial\Drivers\PdfViewerDriver as BasePdfViewerDriver;
use tiFy\Partial\PartialDriverInterface;

class PdfViewerDriver extends BasePdfViewerDriver
{
    /**
     * @inheritDoc
     */
    public function parseParams(): PartialDriverInterface
    {
        parent::parseParams();

        $src = $this->get('src');
        if (is_numeric($src) && ($src = wp_get_attachment_url($src))) {
            $this->set([
                'attrs.data-options.src' => $src,
                'src'                    => $src,
            ]);
        }

        return $this;
    }
}