<?php

declare(strict_types=1);

namespace tiFy\Metabox;

use tiFy\Contracts\View\PlatesFactory;

/**
 * @method string getName()
 * @method mixed getValue(string|null $key = null, mixed $default = null)
 */
interface MetaboxViewInterface extends PlatesFactory
{
}
