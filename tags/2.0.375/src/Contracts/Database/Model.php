<?php declare(strict_types=1);

namespace tiFy\Contracts\Database;

use Illuminate\Database\{
    Eloquent\Model as DbModel,
    Eloquent\Builder as DbBuilder
};

/**
 * @mixin DbBuilder
 * @mixin DbModel
 */
interface Model extends ColumnsAwareTrait, ConnectionAwareTrait
{

}