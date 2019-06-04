<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\Post as CorcelPost;
use tiFy\Wordpress\Contracts\PostBuilder;

/**
 * @method static PostBuilder hasMeta(string|array $meta_key, mixed|null $value, string $operator= '=')
 * @method static PostBuilder hasMetaLike(string $key, string $value)
 */
class Post extends CorcelPost implements PostBuilder
{

}