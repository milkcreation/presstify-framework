<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\Post as CorcelPost;
use tiFy\Wordpress\Contracts\PostBuilder;

/**
 * @method static PostBuilder hasMeta(string|array $meta_key, mixed|null $value, string $operator = '=')
 * @method static PostBuilder hasMetaLike(string $key, string $value)
 */
class Post extends CorcelPost implements PostBuilder
{
    /**
     * @var array
     */
    protected $builtInClasses = [
        \Corcel\Model\Comment::class => \Corcel\Model\Meta\CommentMeta::class,
        Post::class                  => Postmeta::class,
        \Corcel\Model\Term::class    => \Corcel\Model\Meta\TermMeta::class,
        \Corcel\Model\User::class    => \Corcel\Model\Meta\UserMeta::class,
    ];
}