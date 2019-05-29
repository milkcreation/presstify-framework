<?php declare(strict_types=1);

namespace tiFy\Wordpress\Database\Model;

use Corcel\Model\Post as CorcelPost;
use Illuminate\Database\Eloquent\Builder;

/**
 * @see https://github.com/corcel/corcel#posts
 *
 * @mixin Builder
 * @mixin \Corcel\Model\Builder\PostBuilder
 */
class Post extends CorcelPost
{

}
