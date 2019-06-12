<?php declare(strict_types=1);

namespace tiFy\Wordpress\Contracts;

use Corcel\Model\Builder\PostBuilder as CorcelPostBuilder;
use Corcel\Concerns\MetaFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * @see https://github.com/corcel/corcel#posts
 *
 * @mixin Builder
 * @mixin Model
 * @mixin MetaFields
 * @mixin CorcelPostBuilder
 * @method static PostBuilder hasMeta(string|array $meta_key, mixed|null $value, string $operator= '=')
 * @method static PostBuilder hasMetaLike(string $key, string $value)
 */
interface PostBuilder
{

}
