<?php declare(strict_types=1);

namespace tiFy\Wordpress\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Corcel\Model\Builder\PostBuilder as CorcelPostBuilder;

/**
 * @see https://github.com/corcel/corcel#posts
 *
 * @mixin Builder
 * @mixin CorcelPostBuilder
 * @method static PostBuilder hasMeta(string|array $meta_key, mixed|null $value, string $operator= '=')
 * @method static PostBuilder hasMetaLike(string $key, string $value)
 */
interface PostBuilder
{

}
