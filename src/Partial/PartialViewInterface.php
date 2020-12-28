<?php

declare(strict_types=1);

namespace tiFy\Partial;

use tiFy\Contracts\View\PlatesFactory;

/**
 * @method string after()
 * @method string attrs()
 * @method string before()
 * @method string content()
 * @method string getAlias()
 * @method string getId()
 * @method string getIndex()
 */
interface PartialViewInterface extends PlatesFactory
{
}
