<?php declare(strict_types=1);

namespace tiFy\Contracts\Template;

use Illuminate\Database\Eloquent\{Builder, Model};

/**
 * @mixin Builder
 * @mixin Model
 */
interface FactoryDb extends FactoryAwareTrait
{

}