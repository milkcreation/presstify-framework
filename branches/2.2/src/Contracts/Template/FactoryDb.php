<?php

declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Pollen\Database\Drivers\Laravel\Eloquent\AbstractModel;

/**
 * @mixin AbstractModel
 */
interface FactoryDb extends FactoryAwareTrait
{

}