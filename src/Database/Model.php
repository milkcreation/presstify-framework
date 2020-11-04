<?php declare(strict_types=1);

namespace tiFy\Database;

use Illuminate\Database\{
    Eloquent\Model as DbModel,
    Eloquent\Builder as DbBuilder
};
use tiFy\Contracts\Database\Model as ModelContract;
use tiFy\Database\Concerns\{ColumnsAwareTrait, ConnectionAwareTrait};

/**
 * @mixin DbBuilder
 */
abstract class Model extends DbModel implements ModelContract
{
    use ColumnsAwareTrait, ConnectionAwareTrait;
}