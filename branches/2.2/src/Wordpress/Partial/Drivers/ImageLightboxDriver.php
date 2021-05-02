<?php

declare(strict_types=1);

namespace tiFy\Wordpress\Partial\Drivers;

use Pollen\Validation\Validator as v;
use tiFy\Partial\Drivers\ImageLightbox\ImageLightboxItem;
use tiFy\Partial\Drivers\ImageLightbox\ImageLightboxItemInterface;
use tiFy\Partial\Drivers\ImageLightboxDriver as BaseImageLightboxDriver;

class ImageLightboxDriver extends BaseImageLightboxDriver
{
    /**
     * @inheritDoc
     */
    public function fetchItem($item): ?ImageLightboxItemInterface
    {
        if ($item instanceof ImageLightboxItemInterface) {
            return $item;
        } elseif (is_array($item)) {
            if (isset($item['src']) && ($instance = $this->fetchItem($item['src']))) {
                unset($item['src']);
                return $instance->set($item);
            } elseif (isset($item['content'])) {
                return (new ImageLightboxItem())->set($item);
            } else {
                return null;
            }
        } elseif (is_numeric($item) && ($src = wp_get_attachment_url($item))) {
            return (new ImageLightboxItem())->set(
                [
                    'src' => $src,
                ]
            );
        } elseif (is_string($item) && v::url()->validate($item)) {
            return (new ImageLightboxItem())->set(
                [
                    'src' => $item,
                ]
            );
        } else {
            return null;
        }
    }
}